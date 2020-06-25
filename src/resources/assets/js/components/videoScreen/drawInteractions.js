import DrawInteraction from '@biigle/ol/interaction/Draw';
import VectorLayer from '@biigle/ol/layer/Vector';
import VectorSource from '@biigle/ol/source/Vector';
import {Keyboard} from '../../import';
import {Styles} from '../../import';

/**
 * Mixin for the videoScreen component that contains logic for the draw interactions.
 *
 * @type {Object}
 */
export default {
    data() {
        return {
            pendingAnnotation: {},
            autoplayDrawTimeout: null,
        };
    },
    computed: {
        hasSelectedLabel() {
            return !!this.selectedLabel;
        },
        hasNoSelectedLabel() {
            return !this.selectedLabel;
        },
        isDrawing() {
            return this.interactionMode.startsWith('draw');
        },
        isDrawingPoint() {
            return this.interactionMode === 'drawPoint';
        },
        isDrawingRectangle() {
            return this.interactionMode === 'drawRectangle';
        },
        isDrawingCircle() {
            return this.interactionMode === 'drawCircle';
        },
        isDrawingLineString() {
            return this.interactionMode === 'drawLineString';
        },
        isDrawingPolygon() {
            return this.interactionMode === 'drawPolygon';
        },
        hasPendingAnnotation() {
            return this.pendingAnnotation.shape && this.pendingAnnotation.frames.length > 0 && this.pendingAnnotation.points.length > 0;
        },
        cantFinishDrawAnnotation() {
            return !this.hasPendingAnnotation;
        },
        cantFinishTrackAnnotation() {
            return !this.pendingAnnotation.frames || this.pendingAnnotation.frames.length !== 1;
        },
    },
    methods: {
        requireSelectedLabel() {
            this.$emit('requires-selected-label');
            this.resetInteractionMode();
        },
        initPendingAnnotationLayer(map) {
            this.pendingAnnotationSource = new VectorSource();
            this.pendingAnnotationLayer = new VectorLayer({
                opacity: 0.5,
                source: this.pendingAnnotationSource,
                updateWhileAnimating: true,
                updateWhileInteracting: true,
                style: Styles.editing,
            });

            map.addLayer(this.pendingAnnotationLayer);
        },
        draw(name) {
            if (this['isDrawing' + name]) {
                this.resetInteractionMode();
            } else if (this.hasNoSelectedLabel) {
                this.requireSelectedLabel();
            } else if (this.canAdd) {
                this.interactionMode = 'draw' + name;
            }
        },
        drawPoint() {
            this.draw('Point');
        },
        drawRectangle() {
            this.draw('Rectangle');
        },
        drawCircle() {
            this.draw('Circle');
        },
        drawLineString() {
            this.draw('LineString');
        },
        drawPolygon() {
            this.draw('Polygon');
        },
        maybeUpdateDrawInteractionMode(mode) {
            this.resetPendingAnnotation();

            if (this.drawInteraction) {
                this.map.removeInteraction(this.drawInteraction);
                this.drawInteraction = undefined;
            }

            if (this.isDrawing && this.hasSelectedLabel) {
                let shape = mode.slice(4); // Remove the 'draw' prefix.
                this.pause();
                this.drawInteraction = new DrawInteraction({
                    source: this.pendingAnnotationSource,
                    type: shape,
                    style: Styles.editing,
                });
                this.drawInteraction.on('drawend', this.extendPendingAnnotation);
                this.map.addInteraction(this.drawInteraction);
                this.pendingAnnotation.shape = shape;
            }
        },
        finishDrawAnnotation() {
            if (this.isDrawing || this.isUsingPolygonBrush) {
                if (this.hasPendingAnnotation) {
                    this.$emit('create-annotation', this.pendingAnnotation);
                }
                this.resetInteractionMode();
            }
        },
        finishTrackAnnotation() {
            if (this.isDrawing) {
                if (this.hasPendingAnnotation) {
                    this.$emit('track-annotation', this.pendingAnnotation);
                }
                this.resetInteractionMode();
            }
        },
        resetPendingAnnotation() {
            this.pendingAnnotationSource.clear();
            this.pendingAnnotation = {
                shape: '',
                frames: [],
                points: [],
            };
        },
        extendPendingAnnotation(e) {
            let lastFrame = this.pendingAnnotation.frames[this.pendingAnnotation.frames.length - 1];

            if (lastFrame === undefined || lastFrame < this.video.currentTime) {
                this.pendingAnnotation.frames.push(this.video.currentTime);
                this.pendingAnnotation.points.push(this.getPointsFromGeometry(e.feature.getGeometry()));

                if (!this.video.ended && this.autoplayDraw > 0) {
                    this.play();
                    window.clearTimeout(this.autoplayDrawTimeout);
                    this.autoplayDrawTimeout = window.setTimeout(this.pause, this.autoplayDraw * 1000);
                }
            } else {
                this.pendingAnnotationSource.once('addfeature', function (e) {
                    this.removeFeature(e.feature);
                });
            }
        },
    },
    created() {
        this.$once('map-ready', this.initPendingAnnotationLayer);

        if (this.canAdd) {
            this.$watch('interactionMode', this.maybeUpdateDrawInteractionMode);
            Keyboard.on('a', this.drawPoint, 0, this.listenerSet);
            Keyboard.on('s', this.drawRectangle, 0, this.listenerSet);
            Keyboard.on('d', this.drawCircle, 0, this.listenerSet);
            Keyboard.on('f', this.drawLineString, 0, this.listenerSet);
            Keyboard.on('g', this.drawPolygon, 0, this.listenerSet);
            Keyboard.on('Enter', this.finishDrawAnnotation, 0, this.listenerSet);
        }
    },
};
