!function(e){var t={};function i(r){if(t[r])return t[r].exports;var n=t[r]={i:r,l:!1,exports:{}};return e[r].call(n.exports,n,n.exports,i),n.l=!0,n.exports}i.m=e,i.c=t,i.d=function(e,t,r){i.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:r})},i.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},i.t=function(e,t){if(1&t&&(e=i(e)),8&t)return e;if(4&t&&"object"==typeof e&&e&&e.__esModule)return e;var r=Object.create(null);if(i.r(r),Object.defineProperty(r,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var n in e)i.d(r,n,function(t){return e[t]}.bind(null,n));return r},i.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return i.d(t,"a",t),t},i.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},i.p="/",i(i.s=0)}({0:function(e,t,i){i("WfG0"),e.exports=i("zcrr")},WfG0:function(e,t,i){"use strict";i.r(t);function r(e,t,i,r,n,s,o,l){var a,u="function"==typeof e?e.options:e;if(t&&(u.render=t,u.staticRenderFns=i,u._compiled=!0),r&&(u.functional=!0),s&&(u._scopeId="data-v-"+s),o?(a=function(e){(e=e||this.$vnode&&this.$vnode.ssrContext||this.parent&&this.parent.$vnode&&this.parent.$vnode.ssrContext)||"undefined"==typeof __VUE_SSR_CONTEXT__||(e=__VUE_SSR_CONTEXT__),n&&n.call(this,e),e&&e._registeredComponents&&e._registeredComponents.add(o)},u._ssrRegister=a):n&&(a=l?function(){n.call(this,(u.functional?this.parent:this).$root.$options.shadowRoot)}:n),a)if(u.functional){u._injectStyles=a;var c=u.render;u.render=function(e,t){return a.call(t),c(e,t)}}else{var h=u.beforeCreate;u.beforeCreate=h?[].concat(h,a):[a]}return{exports:e,options:u}}var n=r({props:{id:{type:Number,required:!0},thumbUris:{required:!0},removable:{type:Boolean,default:!1},removeTitle:{type:String,default:"Remove this volume"},icon:{type:String}},data:function(){return{index:0,uris:[],hovered:!1}},computed:{progress:function(){return 100*this.index/(this.uris.length-1)+"%"},showFallback:function(){return!1===this.uris[this.index]},showPreview:function(){return this.hovered&&this.someLoaded},someLoaded:function(){return this.uris.reduce((function(e,t){return e||!1!==t}),!1)},iconClass:function(){return this.icon?"fa-"+this.icon:""}},methods:{thumbShown:function(e){return this.index===e&&!this.failed[e]},updateIndex:function(e){var t=this.$el.getBoundingClientRect();this.index=Math.max(0,Math.floor(this.uris.length*(e.clientX-t.left)/t.width))},remove:function(){this.$emit("remove",this.id)},failed:function(e){this.uris.splice(e,1,!1)},updateHovered:function(){this.hovered=!0}},created:function(){Array.isArray(this.thumbUris)?this.uris=this.thumbUris.slice():this.uris=this.thumbUris.split(",")}},(function(){var e=this,t=e.$createElement,i=e._self._c||t;return i("figure",{staticClass:"preview-thumbnail",on:{mousemove:function(t){return e.updateIndex(t)},mouseenter:e.updateHovered}},[e.icon?i("i",{staticClass:"preview-thumbnail__icon fas fa-lg",class:e.iconClass}):e._e(),e._v(" "),e.removable?i("span",{staticClass:"preview-thumbnail__close close",attrs:{title:e.removeTitle},on:{click:function(t){return t.preventDefault(),e.remove(t)}}},[e._v("×")]):e._e(),e._v(" "),e.showPreview?i("div",{staticClass:"preview-thumbnail__images"},e._l(e.uris,(function(t,r){return i("img",{directives:[{name:"show",rawName:"v-show",value:e.thumbShown(r),expression:"thumbShown(i)"}],attrs:{src:t},on:{error:function(t){return e.failed(r)}}})})),0):i("div",{staticClass:"preview-thumbnail__fallback"},[e._t("default")],2),e._v(" "),e._t("caption"),e._v(" "),i("span",{directives:[{name:"show",rawName:"v-show",value:e.someLoaded,expression:"someLoaded"}],staticClass:"preview-thumbnail__progress",style:{width:e.progress}})],2)}),[],!1,null,null,null).exports;biigle.$declare("projects.components.previewThumbnail",n);var s=r({components:{previewThumbnail:n}},void 0,void 0,!1,null,null,null).exports,o=biigle.$require("core.mixins.editor"),l=biigle.$require("messages").handleErrorResponse,a=biigle.$require("core.components.loader"),u=biigle.$require("core.mixins.loader"),c=biigle.$require("core.components.membersPanel"),h=biigle.$require("messages"),d=biigle.$require("api.projects"),m=biigle.$require("core.components.typeahead"),f=r({mixins:[u,o],components:{typeahead:m,loader:a},data:function(){return{project:null,labelTrees:[],availableLabelTrees:[],typeaheadTemplate:'<span v-text="item.name"></span><br><small v-text="item.description"></small>'}},computed:{classObject:function(){return{"panel-warning":this.editing}},hasNoLabelTrees:function(){return 0===this.labelTrees.length},labelTreeIds:function(){return this.labelTrees.map((function(e){return e.id}))},attachableLabelTrees:function(){var e=this;return this.availableLabelTrees.filter((function(t){return-1===e.labelTreeIds.indexOf(t.id)}))}},methods:{fetchAvailableLabelTrees:function(){d.queryAvailableLabelTrees({id:this.project.id}).then(this.availableLabelTreesFetched,l)},availableLabelTreesFetched:function(e){this.availableLabelTrees=e.data.map(this.parseLabelTreeVersionedName)},attachTree:function(e){var t=this;e&&(this.startLoading(),d.attachLabelTree({id:this.project.id},{id:e.id}).then((function(){return t.treeAttached(e)}),l).finally(this.finishLoading))},treeAttached:function(e){for(var t=this.availableLabelTrees.length-1;t>=0;t--)this.availableLabelTrees[t].id===e.id&&this.availableLabelTrees.splice(t,1);this.labelTrees.push(e)},removeTree:function(e){var t=this;this.startLoading(),d.detachLabelTree({id:this.project.id,label_tree_id:e.id}).then((function(){return t.treeRemoved(e)}),l).finally(this.finishLoading)},treeRemoved:function(e){for(var t=this.labelTrees.length-1;t>=0;t--)this.labelTrees[t].id===e.id&&this.labelTrees.splice(t,1);this.availableLabelTrees.push(e)},parseLabelTreeVersionedName:function(e){return e.version&&(e.name=e.name+" @ "+e.version.name),e}},created:function(){this.$once("editing.start",this.fetchAvailableLabelTrees),this.labelTrees=biigle.$require("projects.labelTrees").map(this.parseLabelTreeVersionedName),this.project=biigle.$require("projects.project")}},void 0,void 0,!1,null,null,null).exports,p=r({mixins:[u],data:function(){return{project:null,members:[],roles:[],defaultRole:null,userId:null}},components:{membersPanel:c},methods:{attachMember:function(e){var t=this;this.startLoading(),d.addUser({id:this.project.id,user_id:e.id},{project_role_id:e.role_id}).then((function(){return t.memberAttached(e)}),l).finally(this.finishLoading)},memberAttached:function(e){this.members.push(e)},updateMember:function(e,t){var i=this;this.startLoading(),d.updateUser({id:this.project.id,user_id:e.id},{project_role_id:t.role_id}).then((function(){return i.memberUpdated(e,t)}),l).finally(this.finishLoading)},memberUpdated:function(e,t){e.role_id=t.role_id},removeMember:function(e){var t=this;this.startLoading(),d.removeUser({id:this.project.id,user_id:e.id}).then((function(){return t.memberRemoved(e)}),l).finally(this.finishLoading)},memberRemoved:function(e){for(var t=this.members.length-1;t>=0;t--)this.members[t].id===e.id&&this.members.splice(t,1)}},created:function(){this.project=biigle.$require("projects.project"),this.members=biigle.$require("projects.members"),this.roles=biigle.$require("projects.roles"),this.defaultRole=biigle.$require("projects.defaultRoleId"),this.userId=biigle.$require("projects.userId")}},void 0,void 0,!1,null,null,null).exports,v=r({mixins:[u,o],data:function(){return{project:null,name:null,description:null,userId:null,redirectUrl:null}},computed:{hasDescription:function(){return!!this.description.length},isChanged:function(){return this.name!==this.project.name||this.description!==this.project.description}},methods:{discardChanges:function(){this.name=this.project.name,this.description=this.project.description,this.finishEditing()},leaveProject:function(){confirm('Do you really want to revoke your membership of project "'.concat(this.project.name,'"?'))&&(this.startLoading(),d.removeUser({id:this.project.id,user_id:this.userId}).then(this.projectLeft,l).finally(this.finishLoading))},projectLeft:function(){var e=this;h.success("You left the project. Redirecting..."),setTimeout((function(){return location.href=e.redirectUrl}),2e3)},deleteProject:function(){confirm("Do you really want to delete the project ".concat(this.project.name,"?"))&&(this.startLoading(),d.delete({id:this.project.id}).then(this.projectDeleted,this.maybeForceDeleteProject).finally(this.finishLoading))},maybeForceDeleteProject:function(e){400===e.status?confirm("Deleting this project will delete one or more volumes with all annotations! Do you want to continue?")&&(this.startLoading(),d.delete({id:this.project.id},{force:!0}).then(this.projectDeleted,l).finally(this.finishLoading)):l(e)},projectDeleted:function(){var e=this;h.success("The project was deleted. Redirecting..."),setTimeout((function(){return location.href=e.redirectUrl}),2e3)},saveChanges:function(){this.startLoading(),d.update({id:this.project.id},{name:this.name,description:this.description}).then(this.changesSaved,l).finally(this.finishLoading)},changesSaved:function(){this.project.name=this.name,this.project.description=this.description,this.finishEditing()}},created:function(){this.project=biigle.$require("projects.project"),this.name=this.project.name,this.description=this.project.description,this.userId=biigle.$require("projects.userId"),this.redirectUrl=biigle.$require("projects.redirectUrl")}},void 0,void 0,!1,null,null,null).exports,b=Vue.resource("api/v1/projects{/id}/attachable-volumes"),g=r({mixins:[u,o],data:function(){return{project:null,volumes:[],attachableVolumes:[],filterString:"",fullHeight:0}},components:{previewThumbnail:n,typeahead:m},computed:{filteredVolumes:function(){if(this.hasFiltering){var e=this.filterString.toLowerCase();return this.volumes.filter((function(t){return-1!==t.name.toLowerCase().indexOf(e)}))}return this.volumes},hasFiltering:function(){return this.filterString.length>0},filterInputClass:function(){return this.hasFiltering?"panel-filter--active":""},hasVolumes:function(){return this.volumes.length>0},panelStyle:function(){return this.hasFiltering?{height:this.fullHeight+"px"}:{}},hasNoMatchingVolumes:function(){return this.hasVolumes&&0===this.filteredVolumes.length}},methods:{removeVolume:function(e){var t=this;this.startLoading(),d.detachVolume({id:this.project.id,volume_id:e}).then((function(){return t.volumeRemoved(e)}),(function(i){400===i.status?confirm("The volume you are about to remove belongs only to this project and will be deleted. Are you sure you want to delete this volume?")&&t.forceRemoveVolume(e):l(i)})).finally(this.finishLoading)},forceRemoveVolume:function(e){var t=this;this.startLoading(),d.detachVolume({id:this.project.id,volume_id:e},{force:!0}).then((function(){return t.volumeRemoved(e)}),l).finally(this.finishLoading)},volumeRemoved:function(e){for(var t=this.volumes.length-1;t>=0;t--)this.volumes[t].id===e&&(this.attachableVolumes.unshift(this.volumes[t]),this.volumes.splice(t,1));this.$nextTick(this.updateFullHeight)},hasVolume:function(e){for(var t=this.volumes.length-1;t>=0;t--)if(this.volumes[t].id===e)return!0;return!1},attachVolume:function(e){var t=this;e&&!this.hasVolume(e.id)&&(this.startLoading(),d.attachVolume({id:this.project.id,volume_id:e.id},{}).then((function(){return t.volumeAttached(e)}),l).finally(this.finishLoading))},volumeAttached:function(e){this.volumes.unshift(e);for(var t=this.attachableVolumes.length-1;t>=0;t--)this.attachableVolumes[t].id===e.id&&this.attachableVolumes.splice(t,1);this.$nextTick(this.updateFullHeight)},fetchAttachableVolumes:function(){b.get({id:this.project.id}).then(this.attachableVolumesFetched,l)},attachableVolumesFetched:function(e){this.attachableVolumes=e.data},clearFiltering:function(){this.filterString=""},updateFullHeight:function(){this.fullHeight=this.$el.offsetHeight}},created:function(){this.project=biigle.$require("projects.project"),this.volumes=biigle.$require("projects.volumes"),this.$once("editing.start",this.fetchAttachableVolumes)},mounted:function(){this.updateFullHeight()}},void 0,void 0,!1,null,null,null).exports;biigle.$mount("projects-dashboard-main",s),biigle.$mount("projects-label-trees",f),biigle.$mount("projects-members",p),biigle.$mount("projects-show-volume-list",g),biigle.$mount("projects-title",v)},zcrr:function(e,t){}});