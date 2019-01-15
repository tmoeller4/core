biigle.$component('components.videoScreen', {
    template: '<div class="video-screen">' +
        // '<video :src="src" controls></video>' +
        '<div class="controls">' +
            '<div class="btn-group">' +
                '<control-button v-if="playing" icon="fa-pause" title="Pause" v-on:click="pause"></control-button>' +
                '<control-button v-else icon="fa-play" title="Play" v-on:click="play"></control-button>' +
            '</div>' +
        '</div>' +
    '</div>',
    components: {
        controlButton: biigle.$require('components.controlButton'),
    },
    props: {
        video: {
            type: HTMLVideoElement,
            required: true,
        },
    },
    data: function () {
        return {
            playing: false,
            animationFrameId: null,
            // TODO Count frames in server and put to video metadata. This allows to
            // calculate the frame time for a "ftep frame forward", "step frame backward"
            // button.
        };
    },
    computed: {

    },
    methods: {
        createMap: function () {
            var map = new ol.Map({
                renderer: 'canvas',
                // controls: [
                //     new ol.control.Zoom(),
                //     new ol.control.ZoomToExtent({
                //         tipLabel: 'Zoom to show whole image',
                //         // fontawesome compress icon
                //         label: '\uf066'
                //     }),
                // ],
                interactions: ol.interaction.defaults({
                    altShiftDragRotate: false,
                    doubleClickZoom: false,
                    keyboard: false,
                    shiftDragZoom: false,
                    pinchRotate: false,
                    pinchZoom: false
                }),
            });

            return map;
        },
        createVideoLayer: function () {
            this.videoCanvas.width = this.video.videoWidth;
            this.videoCanvas.height = this.video.videoHeight;

            var extent = [0, 0, this.videoCanvas.width, this.videoCanvas.height];
            var projection = new ol.proj.Projection({
                code: 'biigle-image',
                units: 'pixels',
                extent: extent,
            });

            this.videoLayer = new ol.layer.Image({
                map: this.map,
                source: new ol.source.Canvas({
                    canvas: this.videoCanvas,
                    projection: this.projection,
                    canvasExtent: extent,
                    canvasSize: [extent[0], extent[1]],
                }),
            });

            this.map.setView(new ol.View({
                projection: projection,
                // zoomFactor: 2,
                minResolution: 0.25,
                extent: extent
            }));

            this.map.getView().fit(extent);
        },
        renderVideo: function () {
            this.videoCanvasCtx.drawImage(this.video, 0, 0, this.video.videoWidth, this.video.videoHeight);
            this.map.render();
        },
        startRenderLoop: function () {
            this.renderVideo();
            this.animationFrameId = window.requestAnimationFrame(this.startRenderLoop);
        },
        stopRenderLoop: function () {
            window.cancelAnimationFrame(this.animationFrameId);
            this.animationFrameId = null;
        },
        setPlaying: function () {
            this.playing = true;
        },
        setPaused: function () {
            this.playing = false;
        },
        play: function () {
            this.video.play();
        },
        pause: function () {
            this.video.pause();
        },
    },
    watch: {
        playing: function (playing) {
            if (playing && !this.animationFrameId) {
                this.startRenderLoop();
            } else if (!playing) {
                this.stopRenderLoop();
            }
        },
    },
    created: function () {
        this.map = this.createMap();
        this.videoCanvas = document.createElement('canvas');
        this.videoCanvasCtx = this.videoCanvas.getContext('2d');
        this.video.addEventListener('loadedmetadata', this.createVideoLayer);
        this.video.addEventListener('play', this.setPlaying);
        this.video.addEventListener('pause', this.setPaused);
        this.video.addEventListener('seeked', this.renderVideo);
        this.video.addEventListener('loadeddata', this.renderVideo);
    },
    mounted: function () {
        this.map.setTarget(this.$el);
    },
});
