<?php namespace Dias;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

/**
 * A user.
 */
class User extends Attributable implements AuthenticatableContract, CanResetPasswordContract {

	use Authenticatable, CanResetPassword;

	/**
	 * Validation rules for logging in
	 * 
	 * @var array
	 */
	public static $authRules = array(
		'email'    => 'required|email|max:255',
		'password' => 'required|min:8'
	);

	/**
	 * Validation rules for resetting the password
	 * 
	 * @var array
	 */
	public static $resetRules = array(
		'email'    => 'required|email|max:255',
		'password' => 'required|confirmed|min:8',
		'token'    => 'required',
	);

	/**
	 * Validation rules for registering a new user
	 * 
	 * @var array
	 */
	public static $registerRules = array(
		'email'     => 'required|email|unique:users|max:255',
		'password'  => 'required|min:8|confirmed',
		'firstname' => 'required|alpha|max:127',
		'lastname'  => 'required|alpha|max:127'
	);

	/**
	 * The attributes included in the model's JSON form. All other are hidden.
	 *
	 * @var array
	 */
	protected $visible = array(
		'firstname',
		'lastname',
		'role_id',
	);

	/**
	 * Generates a random string to use as an API key. The key will be stored in
	 * the `api_key` attribute of the user.
	 * 
	 * @return string
	 */
	public function generateApiKey()
	{
		$key = str_random(32);
		$this->api_key = $key;
		return $key;
	}

	/**
	 * The projects, this user is a member of.
	 * 
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	public function projects()
	{
		return $this->belongsToMany('Dias\Project');
	}

	/**
	 * The global role of this user.
	 * 
	 * @return Role
	 */
	public function role()
	{
		return $this->belongsTo('Dias\Role');
	}

	/**
	 * Checks if this user has the global admin role.
	 * 
	 * @return boolean
	 */
	public function isAdmin()
	{
		return $this->role->id === Role::adminId();
	}

	/**
	 * Checks if this user is a member in one of the supplied projects.
	 * 
	 * @param array $ids Project IDs
	 * @return boolean
	 */
	public function canSeeOneOfProjects($ids)
	{
		return $this->projects()->whereIn('id', $ids)->count() > 0;
	}

	/**
	 * Checks if this user is an editor or admin in one of the supplied
	 * projects.
	 * 
	 * @param array $ids Project IDs
	 * @return boolean
	 */
	public function canEditInOneOfProjects($ids)
	{
		foreach ($this->projects()->whereIn('id', $ids)->get() as $project) {
			if ($project->hasEditor($this) || $project->hasAdmin($this))
			{
				return true;
			}
		}
		return false;
	}

	/**
	 * Checks if this user is an admin in one of the supplied projects.
	 * 
	 * @param array $ids Project IDs
	 * @return boolean
	 */
	public function canAdminOneOfProjects($ids)
	{
		foreach ($this->projects()->whereIn('id', $ids)->get() as $project) {
			if ($project->hasAdmin($this))
			{
				return true;
			}
		}
		return false;
	}
}
