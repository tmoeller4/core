@inject('modules', 'Dias\Services\Modules')
<div class="sidebar__foldout settings-foldout" data-ng-class="{open:(foldout=='settings')}">
    <h4>
        Settings
        <button class="btn btn-default pull-right" data-ng-click="toggleFoldout('settings')" title="Collapse this foldout"><span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span></button>
    </h4>

    <div class="settings-foldout__item" data-ng-controller="SettingsAnnotationOpacityController">
        <label title="Set the opacity of annotations on the map">Annotation opacity (<span data-ng-bind="annotation_opacity | number:2"></span>)</label>
        <input type="range" min="0" max="1" step="0.01" data-ng-model="annotation_opacity">
    </div>

    <div class="settings-foldout__item"  data-ng-controller="SettingsAnnotationsCyclingController">
        <div class="form-group">
            <label title="Cycle through all annotations">Cycle through annotations</label>
            <div class="btn-group">
                <button type="button" class="btn btn-default" data-ng-class="{active: cycling()}" data-ng-click="startCycling()" title="Start cycling through all annotations">on</button>
                <button type="button" class="btn btn-default" data-ng-class="{active: !cycling()}" data-ng-click="stopCycling()" title="Stop cycling through all annotations 𝗘𝘀𝗰">off</button>
            </div>
            <div class="btn-group">
                <button class="btn btn-default" data-ng-disabled="!cycling()" data-ng-click="prevAnnotation()" title="Previous anotation 𝗟𝗲𝗳𝘁 𝗮𝗿𝗿𝗼𝘄"><span class="glyphicon glyphicon-step-backward" aria-hidden="true"></span></button>
                <button class="btn btn-default" data-ng-disabled="!cycling()" data-ng-click="nextAnnotation()" title="Next annotation 𝗥𝗶𝗴𝗵𝘁 𝗮𝗿𝗿𝗼𝘄/𝗦𝗽𝗮𝗰𝗲"><span class="glyphicon glyphicon-step-forward" aria-hidden="true"></span></button>
                <button class="btn btn-default" data-ng-disabled="!cycling()" data-ng-click="attachLabel()" title="Attach the current label to the selected annotation 𝗘𝗻𝘁𝗲𝗿"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></button>
            </div>
        </div>
        <p class="help-text">Use the annotation filter to cycle through annotations with certain properties.</p>
    </div>

    <div class="settings-foldout__item"  data-ng-controller="SettingsSectionCyclingController">
        <label title="Cycle through image sections">Cycle through image sections</label>
        <div class="btn-group">
            <button type="button" class="btn btn-default" data-ng-class="{active: cycling()}" data-ng-click="startCycling()" title="Start cycling through image sections">on</button>
            <button type="button" class="btn btn-default" data-ng-class="{active: !cycling()}" data-ng-click="stopCycling()" title="Stop cycling through image sections 𝗘𝘀𝗰">off</button>
        </div>
        <div class="btn-group">
            <button class="btn btn-default" data-ng-disabled="!cycling()" data-ng-click="prevSection()" title="Previous section 𝗟𝗲𝗳𝘁 𝗮𝗿𝗿𝗼𝘄"><span class="glyphicon glyphicon-step-backward" aria-hidden="true"></span></button>
            <button class="btn btn-default" data-ng-disabled="!cycling()" data-ng-click="nextSection()" title="Next section 𝗥𝗶𝗴𝗵𝘁 𝗮𝗿𝗿𝗼𝘄/𝗦𝗽𝗮𝗰𝗲"><span class="glyphicon glyphicon-step-forward" aria-hidden="true"></span></button>
        </div>
    </div>

    @foreach ($modules->getMixins('annotationsSettings') as $module => $nestedMixins)
        @include($module.'::annotationsSettings')
    @endforeach
</div>
