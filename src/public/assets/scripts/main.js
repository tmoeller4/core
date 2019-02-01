biigle.$viewModel("create-video-form",function(t){new Vue({el:t,mixins:[biigle.$require("core.mixins.loader")]})}),biigle.$viewModel("video-container",function(t){var e=biigle.$require("videos.id"),n=biigle.$require("videos.src"),i=biigle.$require("videos.shapes"),o=biigle.$require("videos.api.videoAnnotations"),a=biigle.$require("messages.store"),r=biigle.$require("videos.models.Annotation");new Vue({el:t,mixins:[biigle.$require("core.mixins.loader")],components:{videoScreen:biigle.$require("videos.components.videoScreen"),videoTimeline:biigle.$require("videos.components.videoTimeline"),sidebar:biigle.$require("core.components.sidebar"),sidebarTab:biigle.$require("core.components.sidebarTab"),labelTrees:biigle.$require("labelTrees.components.labelTrees")},data:{video:document.createElement("video"),labelTrees:biigle.$require("videos.labelTrees"),selectedLabel:null,bookmarks:[],annotations:[],seeking:!1},computed:{shapes:function(){var t={};return Object.keys(i).forEach(function(e){t[i[e]]=e}),t},selectedAnnotations:function(){return this.annotations.filter(function(t){return!1!==t.selected})}},methods:{prepareAnnotation:function(t){return new r({data:t})},setAnnotations:function(t){this.annotations=t.body.map(this.prepareAnnotation)},addCreatedAnnotation:function(t){this.annotations.push(this.prepareAnnotation(t.body))},seek:function(t){this.seeking||this.video.currentTime===t||(this.seeking=!0,this.video.currentTime=t)},selectAnnotation:function(t,e){this.selectAnnotations([t],[e])},selectAnnotations:function(t,e){this.deselectAnnotations(),t.forEach(function(t,n){t.selected=e[n]}),e&&e.length>0&&this.seek(e[0])},deselectAnnotations:function(){this.annotations.forEach(function(t){t.selected=!1})},createBookmark:function(t){this.bookmarks.reduce(function(e,n){return e||n.time===t},!1)||this.bookmarks.push({time:t})},createAnnotation:function(t){var n=Object.assign(t,{shape_id:this.shapes[t.shape],label_id:this.selectedLabel?this.selectedLabel.id:0});delete n.shape,o.save({id:e},n).then(this.addCreatedAnnotation,a.handleResponseError)},handleSelectedLabel:function(t){this.selectedLabel=t},handleDeselectedLabel:function(){this.selectedLabel=null},deleteAnnotationsOrKeyframes:function(t){confirm("Are you sure that you want to delete all selected annotations/keyframes?")&&t.forEach(this.deleteAnnotationOrKeyframe)},deleteAnnotationOrKeyframe:function(t){var e=t.annotation.frames.indexOf(t.time);-1!==e&&t.annotation.frames.length>1?(t.annotation.frames.splice(e,1),t.annotation.points.splice(e,1),o.update({id:t.annotation.id},{frames:t.annotation.frames,points:t.annotation.points}).catch(a.handleResponseError)):o.delete({id:t.annotation.id}).then(this.deletedAnnotation(t.annotation)).catch(a.handleResponseError)},deletedAnnotation:function(t){return function(){var e=this.annotations.indexOf(t);-1!==e&&this.annotations.splice(e,1)}.bind(this)},handleVideoSeeked:function(){this.seeking=!1},modifyAnnotations:function(t){t.forEach(this.modifyAnnotation)},modifyAnnotation:function(t){var e=t.annotation.frames.indexOf(t.time);if(-1!==e)t.annotation.points.splice(e,1,t.points);else{for(var n=t.annotation.frames.length-1;n>=0&&!(t.annotation.frames[n]<=t.time);n--);t.annotation.frames.splice(n+1,0,t.time),t.annotation.points.splice(n+1,0,t.points)}o.update({id:t.annotation.id},{frames:t.annotation.frames,points:t.annotation.points}).catch(a.handleResponseError)}},watch:{},created:function(){this.video.muted=!0,this.video.addEventListener("error",function(){a.danger("Error while loading video file.")}),this.video.addEventListener("seeked",this.handleVideoSeeked),this.startLoading();var t=this,n=new Vue.Promise(function(e,n){t.video.addEventListener("loadeddata",e),t.video.addEventListener("error",n)}),i=o.query({id:e});i.then(this.setAnnotations,a.handleResponseError),Vue.Promise.all([n,i]).then(this.finishLoading)},mounted:function(){this.video.src=n}})}),biigle.$declare("videos.api.videoAnnotations",Vue.resource("api/v1/video-annotations{/id}",{},{query:{method:"GET",url:"api/v1/videos{/id}/annotations"},save:{method:"POST",url:"api/v1/videos{/id}/annotations"}})),biigle.$component("videos.components.annotationClip",{template:'<div class="annotation-clip" v-show="duration > 0" :style="style" :class="classObj" @click.stop="select($event)"><keyframe v-for="(frame, i) in keyframes" :frame="frame" @select="selectFrame(i)"></keyframe></div>',components:{keyframe:{template:'<span class="annotation-keyframe" :style="style" :class="classObj" @click.stop="emitSelect"></span>',props:{frame:{type:Object,required:!0}},computed:{offset:function(){return(this.frame.time-this.$parent.startFrame)/this.$parent.clipDuration},style:function(){return{left:100*this.offset+"%","background-color":"#"+this.$parent.color}},classObj:function(){return{"annotation-keyframe--selected":this.frame.selected}}},methods:{emitSelect:function(){this.$emit("select")}}}},props:{annotation:{type:Object,required:!0},label:{type:Object,required:!0},duration:{type:Number,required:!0},elementWidth:{type:Number,required:!0}},data:function(){return{}},computed:{startFrame:function(){return this.annotation.frames[0]},endFrame:function(){return this.annotation.frames[this.annotation.frames.length-1]},offset:function(){return this.startFrame/this.duration*this.elementWidth},clipDuration:function(){return this.endFrame-this.startFrame},width:function(){return this.clipDuration/this.duration*this.elementWidth},color:function(){return this.label.color||"000000"},style:function(){return{left:this.offset+"px",width:this.width+"px","background-color":"#"+this.color+"66"}},keyframes:function(){var t=this.annotation.selected;return this.annotation.frames.map(function(e){return{time:e,selected:t===e}})},selected:function(){return!1!==this.annotation.selected},classObj:function(){return{"annotation-clip--selected":this.selected,"annotation-clip--compact":this.shouldBeCompact,"annotation-clip--more-compact":this.shouldBeMoreCompact}},minTimeBetweenKeyframes:function(){for(var t=1/0,e=this.keyframes.length-1;e>0;e--)t=Math.min(t,this.keyframes[e].time-this.keyframes[e-1].time);return t},minDistanceBetweenKeyframes:function(){return this.minTimeBetweenKeyframes/this.duration*this.elementWidth},shouldBeCompact:function(){return this.minDistanceBetweenKeyframes<=18},shouldBeMoreCompact:function(){return this.minDistanceBetweenKeyframes<=6}},methods:{emitSelect:function(t){this.$emit("select",this.annotation,t)},selectFrame:function(t){this.emitSelect(this.annotation.frames[t])},select:function(t){this.emitSelect(this.startFrame+(t.clientX-t.target.getBoundingClientRect().left)/t.target.clientWidth*this.clipDuration)}},mounted:function(){}}),biigle.$component("videos.components.annotationTrack",{template:'<div class="annotation-track"><div class="annotation-lane" v-for="lane in lanes"><annotation-clip v-for="annotation in lane" :annotation="annotation" :element-width="elementWidth" :label="label" :duration="duration" @select="emitSelect"></annotation-clip></div></div>',components:{annotationClip:biigle.$require("videos.components.annotationClip")},props:{label:{type:Object,required:!0},lanes:{type:Array,required:!0},duration:{type:Number,required:!0},elementWidth:{type:Number,required:!0}},data:function(){return{}},computed:{},methods:{emitSelect:function(t,e){this.$emit("select",t,e)}},watch:{}}),biigle.$component("videos.components.annotationTracks",{template:'<div class="annotation-tracks" @click="emitDeselect" @scroll.stop="handleScroll"><annotation-track v-for="track in tracks" :label="track.label" :lanes="track.lanes" :duration="duration" :element-width="elementWidth" @select="emitSelect"></annotation-track></div>',components:{annotationTrack:biigle.$require("videos.components.annotationTrack")},props:{tracks:{type:Array,required:!0},duration:{type:Number,required:!0},elementWidth:{type:Number,required:!0}},data:function(){return{hasOverflowTop:!1,hasOverflowBottom:!1}},computed:{},methods:{emitSelect:function(t,e){this.$emit("select",t,e)},emitDeselect:function(){this.$emit("deselect")},handleScroll:function(){this.$emit("scroll-y",this.$el.scrollTop),this.updateHasOverflow()},updateHasOverflow:function(){this.hasOverflowTop=this.$el.scrollTop>0;var t=this.$el.scrollHeight-this.$el.clientHeight;this.hasOverflowBottom=t>0&&this.$el.scrollTop<t}},watch:{tracks:function(){this.$nextTick(this.updateHasOverflow)},hasOverflowTop:function(t){this.$emit("overflow-top",t)},hasOverflowBottom:function(t){this.$emit("overflow-bottom",t)}},created:function(){window.addEventListener("resize",this.updateHasOverflow)}}),biigle.$component("videos.components.currentTime",{template:'<div class="current-time" :class="classObject"><span v-text="currentTimeText"></span> <span class="hover-time" v-show="showHoverTime" v-text="hoverTimeText"></span></div>',props:{currentTime:{type:Number,required:!0},hoverTime:{type:Number,default:0},seeking:{type:Boolean,default:!1}},data:function(){return{}},computed:{currentTimeText:function(){return Vue.filter("videoTime")(this.currentTime)},hoverTimeText:function(){return Vue.filter("videoTime")(this.hoverTime)},classObject:function(){return{"current-time--seeking":this.seeking,"current-time--hover":this.showHoverTime}},showHoverTime:function(){return 0!==this.hoverTime}},methods:{},watch:{}}),biigle.$component("videos.components.currentTimeIndicator",{template:'<span class="time-indicator" :style="style"></span>',props:{duration:{type:Number,required:!0},currentTime:{type:Number,required:!0}},data:function(){return{parentWidth:0}},computed:{style:function(){if(this.duration>0){return"transform: translateX("+this.parentWidth*this.currentTime/this.duration+"px);"}}},methods:{updateParentWidth:function(){this.parentWidth=this.$el.parentElement.clientWidth}},mounted:function(){this.updateParentWidth()}}),biigle.$component("videos.components.scrollStrip",{template:'<div class="scroll-strip" @wheel.stop="handleWheel" @mouseleave="handleHideHoverTime"><div class="scroll-strip__scroller" ref="scroller" :style="scrollerStyle" @mousemove="handleUpdateHoverTime"><video-progress :bookmarks="bookmarks" :duration="duration" :element-width="elementWidth" @seek="emitSeek"></video-progress><annotation-tracks :tracks="tracks" :duration="duration" :element-width="elementWidth" @select="emitSelect" @deselect="emitDeselect" @scroll-y="emitScrollY" @overflow-top="updateOverflowTop" @overflow-bottom="updateOverflowBottom"></annotation-tracks><span class="time-indicator" :class="timeIndicatorClass" :style="timeIndicatorStyle"></span><span class="hover-time-indicator" :style="hoverTimeIndicatorStyle" v-show="showHoverTime"></span></div><div class="overflow-shadow overflow-shadow--top" v-show="hasOverflowTop"></div><div class="overflow-shadow overflow-shadow--bottom" v-show="hasOverflowBottom"></div><div class="overflow-shadow overflow-shadow--left" v-show="hasOverflowLeft"></div><div class="overflow-shadow overflow-shadow--right" v-show="hasOverflowRight"></div></div>',components:{videoProgress:biigle.$require("videos.components.videoProgress"),annotationTracks:biigle.$require("videos.components.annotationTracks")},props:{tracks:{type:Array,required:function(){return[]}},bookmarks:{type:Array,required:function(){return[]}},duration:{type:Number,required:!0},currentTime:{type:Number,required:!0},seeking:{type:Boolean,default:!1}},data:function(){return{zoom:1,zoomFactor:.3,scrollFactor:10,initialElementWidth:0,scrollLeft:0,hoverTime:0,hasOverflowTop:!1,hasOverflowBottom:!1}},computed:{currentTimePosition:function(){return this.duration>0?this.elementWidth*this.currentTime/this.duration:0},timeIndicatorClass:function(){return{"time-indicator--seeking":this.seeking}},timeIndicatorStyle:function(){return"transform: translateX("+this.currentTimePosition+"px);"},hoverTimeIndicatorStyle:function(){return"transform: translateX("+this.hoverPosition+"px);"},scrollerStyle:function(){return{width:100*this.zoom+"%",left:this.scrollLeft+"px"}},elementWidth:function(){return this.initialElementWidth*this.zoom},hoverPosition:function(){return this.duration>0?this.elementWidth*this.hoverTime/this.duration:0},showHoverTime:function(){return 0!==this.hoverTime},hasOverflowLeft:function(){return this.scrollLeft<0},hasOverflowRight:function(){return this.elementWidth+this.scrollLeft>this.initialElementWidth}},methods:{updateInitialElementWidth:function(){this.initialElementWidth=this.$el.clientWidth},emitSeek:function(t){this.$emit("seek",t)},emitSelect:function(t,e){this.$emit("select",t,e)},emitDeselect:function(){this.$emit("deselect")},emitScrollY:function(t){this.$emit("scroll-y",t)},handleWheel:function(t){t.shiftKey?0!==t.deltaY&&this.updateZoom(t):t.deltaX<0?this.updateScrollLeft(this.scrollLeft+this.scrollFactor):t.deltaX>0&&this.updateScrollLeft(this.scrollLeft-this.scrollFactor)},updateZoom:function(t){var e=t.clientX-this.$el.getBoundingClientRect().left,n=t.clientX-this.$refs.scroller.getBoundingClientRect().left,i=n/this.elementWidth,o=t.deltaY<0?this.zoomFactor:-1*this.zoomFactor;this.zoom=Math.max(1,this.zoom+o),this.$nextTick(function(){var t=i*this.elementWidth;this.updateScrollLeft(e-t)})},handleHideHoverTime:function(){this.hoverTime=0},handleUpdateHoverTime:function(t){this.hoverTime=(t.clientX-this.$refs.scroller.getBoundingClientRect().left)/this.elementWidth*this.duration},updateScrollLeft:function(t){this.scrollLeft=Math.max(Math.min(0,t),this.initialElementWidth-this.elementWidth)},updateOverflowTop:function(t){this.hasOverflowTop=t},updateOverflowBottom:function(t){this.hasOverflowBottom=t}},watch:{hoverTime:function(t){this.$emit("hover-time",t)},initialElementWidth:function(t,e){this.updateScrollLeft(this.scrollLeft*t/e)}},created:function(){window.addEventListener("resize",this.updateInitialElementWidth);var t=this;biigle.$require("events").$on("sidebar.toggle",function(){t.$nextTick(t.updateInitialElementWidth)}),biigle.$require("keyboard").on(" ",function(t){t.preventDefault()})},mounted:function(){this.$nextTick(this.updateInitialElementWidth)}}),biigle.$component("videos.components.trackHeaders",{template:'<div class="track-headers"><div class="track-header" v-for="track in tracks"><div class="label-name" v-text="track.label.name"></div><div class="lane-dummy" v-for="lane in track.lanes"></div></div></div>',props:{tracks:{type:Array,required:!0},scrollTop:{type:Number,default:0}},data:function(){return{}},computed:{},methods:{},watch:{scrollTop:function(t){this.$el.scrollTop=t}}}),biigle.$component("videos.components.videoProgress",{template:'<div class="video-progress" @click="emitSeek"><bookmark v-for="mark in bookmarks" :bookmark="mark" @select="emitSelectBookmark"></bookmark><tick v-if="hasTicks" v-for="time in ticks" :time="time"></tick></div>',props:{duration:{type:Number,required:!0},bookmarks:{type:Array,default:function(){return[]}},elementWidth:{type:Number,required:!0}},components:{bookmark:{template:'<span class="bookmark" :style="style" @click.stop="emitSelect"></span>',props:{bookmark:{type:Object,required:!0}},computed:{style:function(){return"left: "+this.bookmark.time/this.$parent.duration*this.$parent.elementWidth+"px"}},methods:{emitSelect:function(){this.$emit("select",this.bookmark)}}},tick:{template:'<span class="tick" :style="style" v-text="text"></span>',props:{time:{type:Number,required:!0}},computed:{style:function(){return"left: "+this.time/this.$parent.duration*this.$parent.elementWidth+"px"},text:function(){return Vue.filter("videoTime")(this.time)}}}},data:function(){return{tickSpacing:100}},computed:{tickCount:function(){return Math.floor(this.elementWidth/this.tickSpacing)},ticks:function(){var t=this.duration/this.tickCount;return Array.apply(null,{length:this.tickCount}).map(function(e,n){return t*n})},hasTicks:function(){return this.tickCount>0&&this.duration>0}},methods:{emitSeek:function(t){this.$emit("seek",(t.clientX-t.target.getBoundingClientRect().left)/t.target.clientWidth*this.duration)},emitSelectBookmark:function(t){this.$emit("seek",t.time)}},mounted:function(){}}),biigle.$component("videos.components.videoScreen",{mixins:[biigle.$require("videos.components.videoScreen.videoPlayback"),biigle.$require("videos.components.videoScreen.annotationPlayback"),biigle.$require("videos.components.videoScreen.drawInteractions"),biigle.$require("videos.components.videoScreen.modifyInteractions")],template:'<div class="video-screen"><div class="controls"><div class="btn-group"><control-button v-if="playing" icon="fa-pause" title="Pause 𝗦𝗽𝗮𝗰𝗲𝗯𝗮𝗿" @click="pause"></control-button><control-button v-else icon="fa-play" title="Play 𝗦𝗽𝗮𝗰𝗲𝗯𝗮𝗿" @click="play"></control-button></div><div v-if="canAdd" class="btn-group"><control-button icon="icon-point" title="Start a point annotation 𝗔" @click="drawPoint" :disabled="hasNoSelectedLabel" :hover="false" :open="isDrawingPoint" :active="isDrawingPoint"><control-button icon="fa-check" title="Finish the point annotation" @click="finishDrawAnnotation"></control-button></control-button><control-button icon="icon-rectangle" title="Start a rectangle annotation 𝗦" @click="drawRectangle" :disabled="hasNoSelectedLabel" :hover="false" :open="isDrawingRectangle" :active="isDrawingRectangle"><control-button icon="fa-check" title="Finish the rectangle annotation" @click="finishDrawAnnotation"></control-button></control-button><control-button icon="icon-circle" title="Start a circle annotation 𝗗" @click="drawCircle" :disabled="hasNoSelectedLabel" :hover="false" :open="isDrawingCircle" :active="isDrawingCircle"><control-button icon="fa-check" title="Finish the circle annotation" @click="finishDrawAnnotation"></control-button></control-button><control-button icon="icon-linestring" title="Start a line annotation 𝗙" @click="drawLineString" :disabled="hasNoSelectedLabel" :hover="false" :open="isDrawingLineString" :active="isDrawingLineString"><control-button icon="fa-check" title="Finish the line annotation" @click="finishDrawAnnotation"></control-button></control-button><control-button icon="icon-polygon" title="Start a polygon annotation 𝗚" @click="drawPolygon" :disabled="hasNoSelectedLabel" :hover="false" :open="isDrawingPolygon" :active="isDrawingPolygon"><control-button icon="fa-check" title="Finish the polygon annotation" @click="finishDrawAnnotation"></control-button></control-button></div><div v-if="canEdit" class="btn-group"><control-button v-if="canAdd" icon="fa-bookmark" title="Create a bookmark 𝗕" @click="emitCreateBookmark"></control-button><control-button v-if="canModify" icon="fa-arrows-alt" title="Move selected annotations 𝗠" :active="isTranslating" @click="toggleTranslating"></control-button><control-button v-if="canDelete" icon="fa-trash" title="Delete selected annotations/keyframes 𝗗𝗲𝗹𝗲𝘁𝗲" @click="emitDelete" :disabled="hasNoSelectedAnnotations"></control-button></div></div></div>',components:{controlButton:biigle.$require("annotations.components.controlButton")},props:{annotations:{type:Array,default:function(){return[]}},canAdd:{type:Boolean,default:!1},canModify:{type:Boolean,default:!1},canDelete:{type:Boolean,default:!1},listenerSet:{type:String,default:"default"},selectedAnnotations:{type:Array,default:function(){return[]}},selectedLabel:{type:Object},video:{type:HTMLVideoElement,required:!0}},data:function(){return{interactionMode:"default"}},computed:{canEdit:function(){return this.canAdd||this.canModify||this.canDelete},hasSelectedAnnotations:function(){return this.selectedAnnotations.length>0},hasNoSelectedAnnotations:function(){return!this.hasSelectedAnnotations},isDefaultInteractionMode:function(){return"default"===this.interactionMode}},methods:{createMap:function(){var t=new ol.Map({renderer:"canvas",controls:[new ol.control.Zoom,new ol.control.ZoomToExtent({tipLabel:"Zoom to show whole video",label:""})],interactions:ol.interaction.defaults({altShiftDragRotate:!1,doubleClickZoom:!1,keyboard:!1,shiftDragZoom:!1,pinchRotate:!1,pinchZoom:!1})}),e=biigle.$require("annotations.ol.ZoomToNativeControl");return t.addControl(new e({label:""})),t},initLayersAndInteractions:function(t){var e=biigle.$require("annotations.stores.styles");this.annotationFeatures=new ol.Collection,this.annotationSource=new ol.source.Vector({features:this.annotationFeatures}),this.annotationLayer=new ol.layer.Vector({source:this.annotationSource,updateWhileAnimating:!0,updateWhileInteracting:!0,style:e.features}),this.selectInteraction=new ol.interaction.Select({condition:ol.events.condition.click,style:e.highlight,layers:[this.annotationLayer],multi:!0}),this.selectedFeatures=this.selectInteraction.getFeatures(),this.selectInteraction.on("select",this.handleFeatureSelect),t.addLayer(this.annotationLayer),t.addInteraction(this.selectInteraction)},emitCreateBookmark:function(){this.$emit("create-bookmark",this.video.currentTime)},resetInteractionMode:function(){this.interactionMode="default"},handleFeatureSelect:function(t){this.$emit("select",t.selected.map(function(t){return t.get("annotation")}),t.selected.map(function(){return this.video.currentTime},this))}},watch:{selectedAnnotations:function(t){var e=this.annotationSource,n=this.selectedFeatures;if(e&&n){var i;n.clear(),t.forEach(function(t){(i=e.getFeatureById(t.id))&&n.push(i)})}},isDefaultInteractionMode:function(t){this.selectInteraction.setActive(t)}},created:function(){this.$once("map-ready",this.initLayersAndInteractions),this.map=this.createMap(),this.$emit("map-created",this.map);var t=biigle.$require("keyboard");t.on("Escape",this.resetInteractionMode,0,this.listenerSet),this.canAdd&&t.on("b",this.emitCreateBookmark);var e=this;biigle.$require("events").$on("sidebar.toggle",function(){e.$nextTick(function(){e.map.updateSize()})})},mounted:function(){this.map.setTarget(this.$el)}}),biigle.$component("videos.components.videoTimeline",{template:'<div class="video-timeline"><div class="static-strip"><current-time :current-time="currentTime" :hover-time="hoverTime" :seeking="seeking"></current-time><track-headers ref="trackheaders" :tracks="annotationTracks" :scroll-top="scrollTop"></track-headers></div><scroll-strip :tracks="annotationTracks" :duration="duration" :current-time="currentTime" :bookmarks="bookmarks" :seeking="seeking" @seek="emitSeek" @select="emitSelect" @deselect="emitDeselect" @scroll-y="handleScrollY" @hover-time="updateHoverTime"></scroll-strip></div>',components:{currentTime:biigle.$require("videos.components.currentTime"),trackHeaders:biigle.$require("videos.components.trackHeaders"),scrollStrip:biigle.$require("videos.components.scrollStrip")},props:{annotations:{type:Array,default:function(){return[]}},video:{type:HTMLVideoElement,required:!0},bookmarks:{type:Array,default:function(){return[]}},seeking:{type:Boolean,default:!1}},data:function(){return{animationFrameId:null,refreshRate:30,refreshLastTime:Date.now(),currentTime:0,duration:0,scrollTop:0,hoverTime:0}},computed:{labelMap:function(){var t={};return this.annotations.forEach(function(e){e.labels.forEach(function(e){t.hasOwnProperty(e.label_id)||(t[e.label_id]=e.label)})}),t},annotationTracks:function(){var t={};return this.annotations.forEach(function(e){e.labels.forEach(function(n){t.hasOwnProperty(n.label_id)||(t[n.label_id]=[]),t[n.label_id].push(e)})}),Object.keys(t).map(function(e){return{label:this.labelMap[e],lanes:this.getAnnotationTrackLanes(t[e])}},this)}},methods:{startUpdateLoop:function(){var t=Date.now();t-this.refreshLastTime>=this.refreshRate&&(this.updateCurrentTime(),this.refreshLastTime=t),this.animationFrameId=window.requestAnimationFrame(this.startUpdateLoop)},stopUpdateLoop:function(){this.updateCurrentTime(),window.cancelAnimationFrame(this.animationFrameId),this.animationFrameId=null},updateCurrentTime:function(){this.currentTime=this.video.currentTime},setDuration:function(){this.duration=this.video.duration},emitSeek:function(t){this.$emit("seek",t)},emitSelect:function(t,e){this.$emit("select",t,e)},emitDeselect:function(){this.$emit("deselect")},handleScrollY:function(t){this.scrollTop=t},getAnnotationTrackLanes:function(t){var e=[[]],n=[[]];return t.forEach(function(t){var i=[t.frames[0],t.frames[t.frames.length-1]],o=0,a=!1;t:for(;!a;){if(n[o]){for(var r=e[o].length-1;r>=0;r--)if(this.rangesCollide(e[o][r],i)){o+=1;continue t}}else e[o]=[],n[o]=[];e[o].push(i),n[o].push(t),a=!0}},this),n},rangesCollide:function(t,e){return t[0]>=e[0]&&t[0]<e[1]||t[1]>e[0]&&t[1]<=e[1]||e[0]>=t[0]&&e[0]<t[1]||e[1]>t[0]&&e[1]<=t[1]||t[0]===e[0]&&t[1]===e[1]},updateHoverTime:function(t){this.hoverTime=t}},watch:{},created:function(){this.video.addEventListener("play",this.startUpdateLoop),this.video.addEventListener("pause",this.stopUpdateLoop),this.video.addEventListener("loadedmetadata",this.setDuration),this.video.addEventListener("seeked",this.updateCurrentTime)},mounted:function(){}}),function(){var t=new Date(0);Vue.filter("videoTime",function(e){return t.setTime(1e3*e),t.toISOString().split("T")[1].slice(0,-2)})}(),biigle.$declare("videos.models.Annotation",function(){return Vue.extend({data:function(){return{id:0,frames:[],points:[],video_id:0,shape_id:0,created_at:"",updated_at:"",labels:[],selected:!1,revision:1}},computed:{shape:function(){return biigle.$require("videos.shapes")[this.shape_id]}},watch:{points:function(t){this.revision+=1}}})}),biigle.$component("videos.components.videoScreen.annotationPlayback",function(){return{data:function(){return{renderedAnnotationMap:{}}},computed:{annotationsRevision:function(){return this.annotations.reduce(function(t,e){return t+e.revision},0)},annotationsPreparedToRender:function(){return this.annotations.map(function(t){return{id:t.id,start:t.frames[0],end:t.frames[t.frames.length-1],self:t}}).sort(function(t,e){return t.start-e.start})},preparedInterpolationPoints:function(){var t={};return this.annotations.forEach(function(e){t[e.id]=this.prepareInterpolationPoints(e)},this),t}},methods:{refreshAnnotations:function(t){var e=this.annotationSource,n=this.selectedFeatures,i=this.annotationsPreparedToRender,o=this.renderedAnnotationMap,a={};this.renderedAnnotationMap=a;for(var r,s=[],c=!1,l=0,d=i.length;l<d;l++)if(!(i[l].end<t&&i[l].start!==t)){if(i[l].start>t)break;r=i[l],c=!0,o.hasOwnProperty(r.id)?(a[r.id]=o[r.id],delete o[r.id]):s.push(r.self)}c?Object.values(o).forEach(function(t){e.removeFeature(t),n.remove(t)}):(e.clear(),n.clear());var h=s.map(this.createFeature);h.forEach(function(t){a[t.getId()]=t,!1!==t.get("annotation").selected&&n.push(t)}),h.length>0&&e.addFeatures(h),Object.values(a).forEach(function(e){this.updateGeometry(e,t)},this)},createFeature:function(t){var e=new ol.Feature(this.getGeometryFromPoints(t.shape,t.points[0]));return e.setId(t.id),e.set("annotation",t),t.labels&&t.labels.length>0&&e.set("color",t.labels[0].label.color),e},updateGeometry:function(t,e){var n=t.get("annotation"),i=n.frames;if(!(i.length<=1)){var o;for(o=i.length-1;o>=0&&!(i[o]<=e);o--);if(i[o]===e)t.setGeometry(this.getGeometryFromPoints(n.shape,n.points[o]));else{var a=(e-i[o])/(i[o+1]-i[o]);t.setGeometry(this.getGeometryFromPoints(n.shape,this.interpolatePoints(n,o,a)))}}},prepareInterpolationPoints:function(t){switch(t.shape){case"Rectangle":case"Ellipse":return t.points.map(this.rectangleToInterpolationPoints);case"LineString":case"Polygon":return t.points.map(this.polygonToSvgPath);default:return t.points}},polygonToSvgPath:function(t){return t=t.slice(),t.unshift("M"),t.splice(3,0,"L"),t.join(" ")},interpolatePoints:function(t,e,n){var i=this.preparedInterpolationPoints[t.id],o=i[e],a=i[e+1];switch(t.shape){case"Rectangle":case"Ellipse":return this.interpolationPointsToRectangle(this.interpolateNaive(o,a,n));case"LineString":case"Polygon":return this.interpolatePolymorph(o,a,n);default:return this.interpolateNaive(o,a,n)}},interpolateNaive:function(t,e,n){return t.map(function(t,i){return t+(e[i]-t)*n})},interpolatePolymorph:function(t,e,n){return polymorph.interpolate([t,e])(n).replace(/[MCL\s]+/g," ").trim().split(" ").map(function(t){return parseInt(t,10)})},rectangleToInterpolationPoints:function(t){var e=[t[2]-t[0],t[3]-t[1]],n=[t[6]-t[0],t[7]-t[1]],i=Math.sqrt(n[0]*n[0]+n[1]*n[1]),o=Math.sqrt(e[0]*e[0]+e[1]*e[1]),a=[e[0]/o,e[1]/o],r=[(t[0]+t[2]+t[4]+t[6])/4,(t[1]+t[3]+t[5]+t[7])/4];return[r[0],r[1],a[0],a[1],i,o]},interpolationPointsToRectangle:function(t){var e=[t[2],t[3]],n=[-e[1],e[0]],i=t[4]/2*n[0],o=t[4]/2*n[1],a=t[5]/2*e[0],r=t[5]/2*e[1];return[t[0]-a-i,t[1]-r-o,t[0]+a-i,t[1]+r-o,t[0]+a+i,t[1]+r+o,t[0]-a+i,t[1]-r+o]},getGeometryFromPoints:function(t,e){switch(e=this.convertPointsFromDbToOl(e),t){case"Point":return new ol.geom.Point(e[0]);case"Rectangle":return new ol.geom.Rectangle([e]);case"Polygon":return new ol.geom.Polygon([e]);case"LineString":return new ol.geom.LineString(e);case"Circle":return new ol.geom.Circle(e[0],e[1][0]);case"Ellipse":return new ol.geom.Ellipse([e]);default:return void console.error("Unknown annotation shape: "+t)}},getPointsFromGeometry:function(t){var e;switch(t.getType()){case"Circle":e=[t.getCenter(),[t.getRadius()]];break;case"Polygon":case"Rectangle":case"Ellipse":e=t.getCoordinates()[0];break;case"Point":e=[t.getCoordinates()];break;default:e=t.getCoordinates()}return this.convertPointsFromOlToDb(e)},invertPointsYAxis:function(t){for(var e=this.videoCanvas.height,n=1;n<t.length;n+=2)t[n]=e-t[n];return t},convertPointsFromOlToDb:function(t){return this.invertPointsYAxis(Array.prototype.concat.apply([],t))},convertPointsFromDbToOl:function(t){t=this.invertPointsYAxis(t.slice());for(var e=[],n=0;n<t.length;n+=2)e.push([t[n],t[n+1]||0]);return e}},watch:{},created:function(){this.$on("refresh",this.refreshAnnotations),this.$once("map-ready",function(){this.$watch("annotationsRevision",function(){this.refreshAnnotations(this.video.currentTime)})})}}}),biigle.$component("videos.components.videoScreen.drawInteractions",function(){return{data:function(){return{pendingAnnotation:{}}},computed:{hasSelectedLabel:function(){return!!this.selectedLabel},hasNoSelectedLabel:function(){return!this.selectedLabel},isDrawing:function(){return this.interactionMode.startsWith("draw")},isDrawingPoint:function(){return"drawPoint"===this.interactionMode},isDrawingRectangle:function(){return"drawRectangle"===this.interactionMode},isDrawingCircle:function(){return"drawCircle"===this.interactionMode},isDrawingLineString:function(){return"drawLineString"===this.interactionMode},isDrawingPolygon:function(){return"drawPolygon"===this.interactionMode}},methods:{initPendingAnnotationLayer:function(t){var e=biigle.$require("annotations.stores.styles");this.pendingAnnotationSource=new ol.source.Vector,this.pendingAnnotationLayer=new ol.layer.Vector({opacity:.5,source:this.pendingAnnotationSource,updateWhileAnimating:!0,updateWhileInteracting:!0,style:e.editing}),t.addLayer(this.pendingAnnotationLayer)},draw:function(t){this["isDrawing"+t]?this.resetInteractionMode():this.canAdd&&this.hasSelectedLabel&&(this.interactionMode="draw"+t)},drawPoint:function(){this.draw("Point")},drawRectangle:function(){this.draw("Rectangle")},drawCircle:function(){this.draw("Circle")},drawLineString:function(){this.draw("LineString")},drawPolygon:function(){this.draw("Polygon")},maybeUpdateDrawInteractionMode:function(t){if(this.resetPendingAnnotation(),this.drawInteraction&&(this.map.removeInteraction(this.drawInteraction),this.drawInteraction=void 0),this.isDrawing&&this.hasSelectedLabel){var e=t.slice(4);this.pause(),this.drawInteraction=new ol.interaction.Draw({source:this.pendingAnnotationSource,type:e,style:biigle.$require("annotations.stores.styles").editing}),this.drawInteraction.on("drawend",this.extendPendingAnnotation),this.map.addInteraction(this.drawInteraction),this.pendingAnnotation.shape=e}},finishDrawAnnotation:function(){this.$emit("create-annotation",this.pendingAnnotation),this.resetInteractionMode()},resetPendingAnnotation:function(){this.pendingAnnotationSource.clear(),this.pendingAnnotation={shape:"",frames:[],points:[]}},
extendPendingAnnotation:function(t){var e=this.pendingAnnotation.frames[this.pendingAnnotation.frames.length-1];void 0===e||e<this.video.currentTime?(this.pendingAnnotation.frames.push(this.video.currentTime),this.pendingAnnotation.points.push(this.getPointsFromGeometry(t.feature.getGeometry()))):this.pendingAnnotationSource.once("addfeature",function(t){this.removeFeature(t.feature)})}},watch:{},created:function(){if(this.$once("map-ready",this.initPendingAnnotationLayer),this.canAdd){var t=biigle.$require("keyboard");t.on("a",this.drawPoint,0,this.listenerSet),t.on("s",this.drawRectangle,0,this.listenerSet),t.on("d",this.drawCircle,0,this.listenerSet),t.on("f",this.drawLineString,0,this.listenerSet),t.on("g",this.drawPolygon,0,this.listenerSet),this.$watch("interactionMode",this.maybeUpdateDrawInteractionMode)}}}}),biigle.$component("videos.components.videoScreen.modifyInteractions",function(){return{data:function(){return{isTranslating:!1}},computed:{},methods:{initModifyInteraction:function(t){this.featureRevisionMap={},this.modifyInteraction=new ol.interaction.Modify({features:this.selectInteraction.getFeatures(),deleteCondition:function(t){return ol.events.condition.shiftKeyOnly(t)&&ol.events.condition.singleClick(t)}}),this.modifyInteraction.on("modifystart",this.handleModifyStart),this.modifyInteraction.on("modifyend",this.handleModifyEnd),t.addInteraction(this.modifyInteraction)},handleModifyStart:function(t){t.features.forEach(function(t){this.featureRevisionMap[t.getId()]=t.getRevision()},this)},handleModifyEnd:function(t){var e=t.features.getArray().filter(function(t){return this.featureRevisionMap[t.getId()]!==t.getRevision()},this).map(function(t){return{annotation:t.get("annotation"),points:this.getPointsFromGeometry(t.getGeometry()),time:this.video.currentTime}},this);e.length>0&&this.$emit("modify",e)},maybeUpdateModifyInteractionMode:function(t){this.modifyInteraction&&this.modifyInteraction.setActive(t)},emitDelete:function(){this.canDelete&&this.hasSelectedAnnotations&&this.$emit("delete",this.selectedAnnotations.map(function(t){return{annotation:t,time:this.video.currentTime}},this))},toggleTranslating:function(){this.resetInteractionMode(),this.isTranslating=!this.isTranslating},initTranslateInteraction:function(t){var e=biigle.$require("annotations.ol.ExtendedTranslateInteraction");this.translateInteraction=new e({features:this.selectedFeatures,map:t}),this.translateInteraction.setActive(!1),this.translateInteraction.on("translatestart",this.handleModifyStart),this.translateInteraction.on("translateend",this.handleModifyEnd),this.map.addInteraction(this.translateInteraction)},maybeUpdateIsTranslating:function(t){this.translateInteraction&&!t&&(this.isTranslating=!1)},resetTranslating:function(){this.isTranslating=!1}},watch:{isTranslating:function(t){this.translateInteraction&&(this.translateInteraction.setActive(t),t?this.modifyInteraction.setActive(!1):this.isDefaultInteractionMode&&this.modifyInteraction.setActive(!0))}},created:function(){var t=biigle.$require("keyboard");this.canModify&&(this.$once("map-created",function(){this.$once("map-ready",this.initModifyInteraction),this.$once("map-ready",this.initTranslateInteraction)}),this.$watch("isDefaultInteractionMode",this.maybeUpdateModifyInteractionMode),this.$watch("isDefaultInteractionMode",this.maybeUpdateIsTranslating),t.on("m",this.toggleTranslating,0,this.listenerSet),t.on("Escape",this.resetTranslating,0,this.listenerSet)),this.canDelete&&t.on("Delete",this.emitDelete)}}}),biigle.$component("videos.components.videoScreen.videoPlayback",function(){return{data:function(){return{playing:!1,animationFrameId:null,refreshRate:30,refreshLastTime:Date.now()}},computed:{},methods:{initVideoLayer:function(t){var e=t[0];this.videoCanvas.width=this.video.videoWidth,this.videoCanvas.height=this.video.videoHeight;var n=[0,0,this.videoCanvas.width,this.videoCanvas.height],i=new ol.proj.Projection({code:"biigle-image",units:"pixels",extent:n});this.videoLayer=new ol.layer.Image({name:"videoLayer",source:new ol.source.Canvas({canvas:this.videoCanvas,projection:i,canvasExtent:n,canvasSize:[n[0],n[1]]})}),e.addLayer(this.videoLayer),e.setView(new ol.View({projection:i,minResolution:.25,extent:n})),e.getView().fit(n)},renderVideo:function(){this.videoCanvasCtx.drawImage(this.video,0,0,this.video.videoWidth,this.video.videoHeight),this.videoLayer.changed();var t=Date.now();t-this.refreshLastTime>=this.refreshRate&&(this.$emit("refresh",this.video.currentTime),this.refreshLastTime=t)},startRenderLoop:function(){this.renderVideo(),this.animationFrameId=window.requestAnimationFrame(this.startRenderLoop)},stopRenderLoop:function(){window.cancelAnimationFrame(this.animationFrameId),this.animationFrameId=null},setPlaying:function(){this.playing=!0},setPaused:function(){this.playing=!1},togglePlaying:function(){this.playing?this.pause():this.play()},play:function(){this.video.play()},pause:function(){this.video.pause()},emitMapReady:function(){this.$emit("map-ready",this.map)}},watch:{playing:function(t){t&&!this.animationFrameId?this.startRenderLoop():t||this.stopRenderLoop()}},created:function(){this.videoCanvas=document.createElement("canvas"),this.videoCanvasCtx=this.videoCanvas.getContext("2d"),this.video.addEventListener("play",this.setPlaying),this.video.addEventListener("pause",this.setPaused),this.video.addEventListener("seeked",this.renderVideo),this.video.addEventListener("loadeddata",this.renderVideo);var t=this,e=new Vue.Promise(function(e,n){t.$once("map-created",e)}),n=new Vue.Promise(function(e,n){t.video.addEventListener("loadedmetadata",e),t.video.addEventListener("error",n)});Vue.Promise.all([e,n]).then(this.initVideoLayer).then(this.emitMapReady),biigle.$require("keyboard").on(" ",this.togglePlaying)}}});