biigle.$component('components.scrollStrip', {
    template: '<div class="scroll-strip">' +
        '<video-progress' +
            ' :duration="duration"' +
            ' @seek="emitSeek"' +
            '></video-progress>' +
        '<annotation-tracks' +
            ' :annotations="annotations"' +
            ' :duration="duration"' +
            ' :element-width="elementWidth"' +
            '></annotation-tracks>' +
        '<span class="time-indicator" :style="indicatorStyle"></span>' +
    '</div>',
    components: {
        videoProgress: biigle.$require('components.videoProgress'),
        annotationTracks: biigle.$require('components.annotationTracks'),
    },
    props: {
        annotations: {
            type: Array,
            required: function () {
                return [];
            },
        },
        duration: {
            type: Number,
            required: true,
        },
        currentTime: {
            type: Number,
            required: true,
        },
    },
    data: function () {
        return {
            elementWidth: 0,
        };
    },
    computed: {
        currentTimeOffset: function () {
            if (this.duration > 0) {
                return Math.round(this.elementWidth * this.currentTime / this.duration);
            }

            return 0;
        },
        indicatorStyle: function () {
            return 'transform: translateX(' + this.currentTimeOffset + 'px);';
        },
    },
    methods: {
        updateElementWidth: function () {
            this.elementWidth = this.$el.clientWidth;
        },
        emitSeek: function (offset) {
            this.$emit('seek', offset / this.elementWidth * this.duration);
        },
    },
    mounted: function () {
        this.updateElementWidth();
        window.addEventListener('resize', this.updateElementWidth);
    },
});
