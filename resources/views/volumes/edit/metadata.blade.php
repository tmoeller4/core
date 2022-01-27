<div id="volume-metadata-upload" class="panel panel-default">
    <div class="panel-heading">
        Image metadata
        <span class="pull-right">
            <span class="loader" v-bind:class="{'loader--active':loading}"></span>
            <dropdown tag="span" v-if="hasIfdo" v-cloak>
                <button class="btn btn-default btn-xs dropdown-toggle" type="button" title="Manage the iFDO file attached to this volume" :disabled="loading">iFDO <span class="caret"></span></button>
                <template slot="dropdown">
                    <li>
                        <a href="{{url("api/v1/volumes/{$volume->id}/ifdo")}}" title="Download the iFDO file">Download</a>
                    </li>
                    <li>
                        <a href="#" title="Delete the iFDO file" v-on:click.prevent="deleteIfdo">Delete</a>
                    </li>
                </template>
            </dropdown>
        </span>
    </div>
    <div class="panel-body">
        <tabs>
            <tab title="iFDO file">
                <p>
                    Upload an <a href="https://marine-imaging.com/fair/ifdos/iFDO-overview">iFDO file</a> to attach it to the volume and update the image metadata.
                </p>
                <form class="form" v-on:submit.prevent="submitIfdo">
                    <div class="form-group">
                        <input class="hidden" ref="ifdoInput" type="file" name="file" v-on:change="handleIfdo" accept=".yml,.yaml">
                        <button class="btn btn-default" type="submit" :disabled="loading">Upload iFDO</button>
                    </div>
                </form>
            </tab>
            <tab title="CSV file" v-cloak>
                <p>
                    Upload a CSV file to update the metadata of the images of this volume.
                </p>
                <form class="form" v-on:submit.prevent="submitCsv">
                    <div class="form-group">
                        <input class="hidden" ref="csvInput" type="file" name="file" v-on:change="uploadCsv" accept=".csv,text/csv,application/csv">
                        <button class="btn btn-default" type="submit" :disabled="loading">Upload CSV</button>
                    </div>
                </form>
            </tab>
        </tabs>
        <div class="alert alert-danger" v-if="error" v-text="error" v-cloak></div>
        <div class="alert alert-success" v-if="success" v-cloak>
            The image metadata was successfully updated.
        </div>
        <p class="text-muted">
            Learn more about image metadata and the CSV file format in the <a href="{{route('manual-tutorials', ['volumes', 'image-metadata'])}}">manual</a>.
        </p>
    </div>
</div>
