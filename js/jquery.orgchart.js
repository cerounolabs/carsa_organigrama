/*
 * jQuery OrgChart Plugin
 * https://github.com/dabeng/OrgChart
 *
 * Copyright 2016, dabeng
 * https://github.com/dabeng
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */
'use strict';

(function (factory) {
    if (typeof module === 'object' && typeof module.exports === 'object') {
        factory(require('jquery'), window, document);
    } else {
        factory(jQuery, window, document);
    }
}(function ($, window, document, undefined) {
    var OrgChart = function (elem, opts) {
        this.$chartContainer = $(elem);
        this.opts = opts;
        this.defaultOptions = {
            'nodeTitle': 'name',
            'nodeId': 'id',
            'toggleSiblingsResp': false,
            'visibleLevel': 999,
            'chartClass': '',
            'exportButton': true,
            'exportFilename': 'Organigrama',
            'exportFileextension': 'pdf',
            'parentNodeSymbol': 'fa-users',
            'draggable': false,
            'direction': 't2b',
            'pan': false,
            'zoom': false,
            'zoominLimit': 7,
            'zoomoutLimit': 0.5
        };
    };
    //
    OrgChart.prototype = {
        //
        init: function (opts) {
            var that = this;
            this.options = $.extend({}, this.defaultOptions, this.opts, opts);
            // build the org-chart
            var $chartContainer = this.$chartContainer;
            if (this.$chart) {
                this.$chart.remove();
            }
            var data = this.options.data;
            var $chart = this.$chart = $('<div>', {
                'data': { 'options': this.options },
                'class': 'orgchart' + (this.options.chartClass !== '' ? ' ' + this.options.chartClass : '') + (this.options.direction !== 't2b' ? ' ' + this.options.direction : ''),
                'click': function(event) {
                    if (!$(event.target).closest('.node').length) {
                        $chart.find('.node.focused').removeClass('focused');
                    }
                }
            });
            if (typeof MutationObserver !== 'undefined') {
                this.triggerInitEvent();
            }
            if ($.type(data) === 'object') {
                if (data instanceof $) { // ul datasource
                    this.buildHierarchy($chart, this.buildJsonDS(data.children()), 0, this.options);
                } else { // local json datasource
                    this.buildHierarchy($chart, this.options.ajaxURL ? data : this.attachRel(data, '00'));
                }
            } else {
                $chart.append('<i class="fa fa-circle-o-notch fa-spin spinner"></i>');
                $.ajax({
                    'url': data,
                    'dataType': 'json'
                })
                    .done(function(data, textStatus, jqXHR) {
                        that.buildHierarchy($chart, that.options.ajaxURL ? data : that.attachRel(data, '00'), 0, that.options);
                    })
                    .fail(function(jqXHR, textStatus, errorThrown) {
                        console.log("orgChart: " + errorThrown);
                    })
                    .always(function() {
                        $chart.children('.spinner').remove();
                    });
            }
            $chartContainer.append($chart);

            // append the export button
            if (this.options.exportButton && !$chartContainer.find('.oc-export-btn').length) {
                this.attachExportButton();
            }

            if (this.options.pan) {
                this.bindPan();
            }

            if (this.options.zoom) {
                this.bindZoom();
            }

            return this;
        },
        //
        triggerInitEvent: function () {
            var that = this;
            var mo = new MutationObserver(function (mutations) {
                mo.disconnect();
                initTime:
                    for (var i = 0; i < mutations.length; i++) {
                        for (var j = 0; j < mutations[i].addedNodes.length; j++) {
                            if (mutations[i].addedNodes[j].classList.contains('orgchart')) {
                                if (that.options.initCompleted && typeof that.options.initCompleted === 'function') {
                                    that.options.initCompleted(that.$chart);
                                    var initEvent = $.Event('init.orgchart');
                                    that.$chart.trigger(initEvent);
                                    break initTime;
                                }
                            }
                        }
                    }
            });
            mo.observe(this.$chartContainer[0], { childList: true });
        },
        //
        attachExportButton: function () {
            var that = this;
            var $exportBtn = $('<button>', {
                'class': 'oc-export-btn' + (this.options.chartClass !== '' ? ' ' + this.options.chartClass : ''),
                'text': 'Export',
                'click': function(e) {



                    var htmltabla=$("#resumen").html();

                    var htt="<table id ='tablainfo' style='border: 1px solid #c59d4d;float:left;'>  "+htmltabla+" </table>";
                    $('#tablainfo tbody .tr td').css("text-aling","left");

                    $(".orgchart").append(htt);

                    $(".orgchart").prepend("<br><h1 id='tituloger' style='color:black'>"+localStorage.getItem("actualgerencia")+"</h1><br>");


                    e.preventDefault();
                    that.export();
                }
            });
            this.$chartContainer.append($exportBtn);
        },
        setOptions: function (opts, val) {
            if (typeof opts === 'string') {
                if (opts === 'pan') {
                    if (val) {
                        this.bindPan();
                    } else {
                        this.unbindPan();
                    }
                }
                if (opts === 'zoom') {
                    if (val) {
                        this.bindZoom();
                    } else {
                        this.unbindZoom();
                    }
                }
            }
            if (typeof opts === 'object') {
                if (opts.data) {
                    this.init(opts);
                } else {
                    if (typeof opts.pan !== 'undefined') {
                        if (opts.pan) {
                            this.bindPan();
                        } else {
                            this.unbindPan();
                        }
                    }
                    if (typeof opts.zoom !== 'undefined') {
                        if (opts.zoom) {
                            this.bindZoom();
                        } else {
                            this.unbindZoom();
                        }
                    }
                }
            }

            return this;
        },
        //
        panStartHandler: function (e) {
            var $chart = $(e.delegateTarget);
            if ($(e.target).closest('.node').length || (e.touches && e.touches.length > 1)) {
                $chart.data('panning', false);
                return;
            } else {
                $chart.css('cursor', 'move').data('panning', true);
            }
            var lastX = 0;
            var lastY = 0;
            var lastTf = $chart.css('transform');
            if (lastTf !== 'none') {
                var temp = lastTf.split(',');
                if (lastTf.indexOf('3d') === -1) {
                    lastX = parseInt(temp[4]);
                    lastY = parseInt(temp[5]);
                } else {
                    lastX = parseInt(temp[12]);
                    lastY = parseInt(temp[13]);
                }
            }
            var startX = 0;
            var startY = 0;
            if (!e.targetTouches) { // pand on desktop
                startX = e.pageX - lastX;
                startY = e.pageY - lastY;
            } else if (e.targetTouches.length === 1) { // pan on mobile device
                startX = e.targetTouches[0].pageX - lastX;
                startY = e.targetTouches[0].pageY - lastY;
            } else if (e.targetTouches.length > 1) {
                return;
            }
            $chart.on('mousemove touchmove',function(e) {
                if (!$chart.data('panning')) {
                    return;
                }
                var newX = 0;
                var newY = 0;
                if (!e.targetTouches) { // pand on desktop
                    newX = e.pageX - startX;
                    newY = e.pageY - startY;
                } else if (e.targetTouches.length === 1) { // pan on mobile device
                    newX = e.targetTouches[0].pageX - startX;
                    newY = e.targetTouches[0].pageY - startY;
                } else if (e.targetTouches.length > 1) {
                    return;
                }
                var lastTf = $chart.css('transform');
                if (lastTf === 'none') {
                    if (lastTf.indexOf('3d') === -1) {
                        $chart.css('transform', 'matrix(1, 0, 0, 1, ' + newX + ', ' + newY + ')');
                    } else {
                        $chart.css('transform', 'matrix3d(1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1, 0, ' + newX + ', ' + newY + ', 0, 1)');
                    }
                } else {
                    var matrix = lastTf.split(',');
                    if (lastTf.indexOf('3d') === -1) {
                        matrix[4] = ' ' + newX;
                        matrix[5] = ' ' + newY + ')';
                    } else {
                        matrix[12] = ' ' + newX;
                        matrix[13] = ' ' + newY;
                    }
                    $chart.css('transform', matrix.join(','));
                }
            });
        },
        //
        panEndHandler: function (e) {
            if (e.data.chart.data('panning')) {
                e.data.chart.data('panning', false).css('cursor', 'default').off('mousemove');
            }
        },
        //
        bindPan: function () {
            this.$chartContainer.css('overflow', 'hidden');
            this.$chart.on('mousedown touchstart', this.panStartHandler);
            $(document).on('mouseup touchend', { 'chart': this.$chart }, this.panEndHandler);
        },
        //
        unbindPan: function () {
            this.$chartContainer.css('overflow', 'auto');
            this.$chart.off('mousedown touchstart', this.panStartHandler);
            $(document).off('mouseup touchend', this.panEndHandler);
        },
        //
        zoomWheelHandler: function (e) {
            var oc = e.data.oc;
            e.preventDefault();
            var newScale  = 1 + (e.originalEvent.deltaY > 0 ? -0.2 : 0.2);
            oc.setChartScale(oc.$chart, newScale);
        },
        //
        zoomStartHandler: function (e) {
            if(e.touches && e.touches.length === 2) {
                var oc = e.data.oc;
                oc.$chart.data('pinching', true);
                var dist = oc.getPinchDist(e);
                oc.$chart.data('pinchDistStart', dist);
            }
        },
        zoomingHandler: function (e) {
            var oc = e.data.oc;
            if(oc.$chart.data('pinching')) {
                var dist = oc.getPinchDist(e);
                oc.$chart.data('pinchDistEnd', dist);
            }
        },
        zoomEndHandler: function (e) {
            var oc = e.data.oc;
            if(oc.$chart.data('pinching')) {
                oc.$chart.data('pinching', false);
                var diff = oc.$chart.data('pinchDistEnd') - oc.$chart.data('pinchDistStart');
                if (diff > 0) {
                    oc.setChartScale(oc.$chart, 1.2);
                } else if (diff < 0) {
                    oc.setChartScale(oc.$chart, 0.8);
                }
            }
        },
        //
        bindZoom: function () {
            this.$chartContainer.on('wheel', { 'oc': this }, this.zoomWheelHandler);
            this.$chartContainer.on('touchstart', { 'oc': this }, this.zoomStartHandler);
            $(document).on('touchmove', { 'oc': this }, this.zoomingHandler);
            $(document).on('touchend', { 'oc': this }, this.zoomEndHandler);
        },
        unbindZoom: function () {
            this.$chartContainer.off('wheel', this.zoomWheelHandler);
            this.$chartContainer.off('touchstart', this.zoomStartHandler);
            $(document).off('touchmove', this.zoomingHandler);
            $(document).off('touchend', this.zoomEndHandler);
        },
        //
        getPinchDist: function (e) {
            return Math.sqrt((e.touches[0].clientX - e.touches[1].clientX) * (e.touches[0].clientX - e.touches[1].clientX) +
                (e.touches[0].clientY - e.touches[1].clientY) * (e.touches[0].clientY - e.touches[1].clientY));
        },
        //
        setChartScale: function ($chart, newScale) {
            var opts = $chart.data('options');
            var lastTf = $chart.css('transform');
            var matrix = '';
            var targetScale = 1;
            if (lastTf === 'none') {
                $chart.css('transform', 'scale(' + newScale + ',' + newScale + ')');
            } else {
                matrix = lastTf.split(',');
                if (lastTf.indexOf('3d') === -1) {
                    targetScale = Math.abs(window.parseFloat(matrix[3]) * newScale);
                    if (targetScale > opts.zoomoutLimit && targetScale < opts.zoominLimit) {
                        $chart.css('transform', lastTf + ' scale(' + newScale + ',' + newScale + ')');
                    }
                } else {
                    targetScale = Math.abs(window.parseFloat(matrix[1]) * newScale);
                    if (targetScale > opts.zoomoutLimit && targetScale < opts.zoominLimit) {
                        $chart.css('transform', lastTf + ' scale3d(' + newScale + ',' + newScale + ', 1)');
                    }
                }
            }
        },
        //
        buildJsonDS: function ($li) {
            var that = this;
            var subObj = {
                'name': $li.contents().eq(0).text().trim(),
                'relationship': ($li.parent().parent().is('li') ? '1': '0') + ($li.siblings('li').length ? 1: 0) + ($li.children('ul').length ? 1 : 0)
            };
            if ($li.attr('data-id')) {
                subObj.id = $li.attr('data-id');
            }
            $li.children('ul').children().each(function() {
                if (!subObj.children) { subObj.children = []; }
                subObj.children.push(that.buildJsonDS($(this)));
            });
            return subObj;
        },
        //
        attachRel: function (data, flags) {
            var that = this;
            data.relationship = flags + (data.children && data.children.length > 0 ? 1 : 0);
            if (data.children) {
                data.children.forEach(function(item) {
                    that.attachRel(item, '1' + (data.children.length > 1 ? 1 : 0));
                });
            }
            return data;
        },
        //
        loopChart: function ($chart) {
            var that = this;
            var $tr = $chart.find('tr:first');
            var subObj = { 'id': $tr.find('.node')[0].id };
            $tr.siblings(':last').children().each(function() {
                if (!subObj.children) { subObj.children = []; }
                subObj.children.push(that.loopChart($(this)));
            });
            return subObj;
        },
        //
        getHierarchy: function () {
            if (typeof this.$chart === 'undefined') {
                return 'Error: orgchart does not exist'
            } else {
                if (!this.$chart.find('.node').length) {
                    return 'Error: nodes do not exist'
                } else {
                    var valid = true;
                    this.$chart.find('.node').each(function () {
                        if (!this.id) {
                            valid = false;
                            return false;
                        }
                    });
                    if (!valid) {
                        return 'Error: All nodes of orghcart to be exported must have data-id attribute!';
                    }
                }
            }
            return this.loopChart(this.$chart);
        },
        // detect the exist/display state of related node
        getNodeState: function ($node, relation) {
            var $target = {};
            var relation = relation || 'self';
            if (relation === 'parent') {
                $target = $node.closest('.nodes').siblings(':first');
                if ($target.length) {
                    if ($target.is('.hidden') || (!$target.is('.hidden') && $target.closest('.nodes').is('.hidden'))) {
                        return { 'exist': true, 'visible': false };
                    }
                    return { 'exist': true, 'visible': true };
                }
            } else if (relation === 'children') {
                $target = $node.closest('tr').siblings(':last');
                if ($target.length) {
                    if (!$target.is('.hidden')) {
                        return { 'exist': true, 'visible': true };
                    }
                    return { 'exist': true, 'visible': false };
                }
            } else if (relation === 'siblings') {
                $target = $node.closest('table').parent().siblings();
                if ($target.length) {
                    if (!$target.is('.hidden') && !$target.parent().is('.hidden')) {
                        return { 'exist': true, 'visible': true };
                    }
                    return { 'exist': true, 'visible': false };
                }
            } else {
                $target = $node;
                if ($target.length) {
                    if (!(($target.closest('.nodes').length && $target.closest('.nodes').is('.hidden')) ||
                        ($target.closest('table').parent().length && $target.closest('table').parent().is('.hidden')) ||
                        ($target.parent().is('li') && ($target.closest('ul').is('.hidden') || $target.closest('verticalNodes').is('.hidden')))
                    )) {
                        return { 'exist': true, 'visible': true };
                    }
                    return { 'exist': true, 'visible': false };
                }
            }
            return { 'exist': false, 'visible': false };
        },
        // find the related nodes
        getRelatedNodes: function ($node, relation) {
            if (!$node || !($node instanceof $) || !$node.is('.node')) {
                return $();
            }
            if (relation === 'parent') {
                return $node.closest('.nodes').parent().children(':first').find('.node');
            } else if (relation === 'children') {
                return $node.closest('tr').siblings('.nodes').children().find('.node:first');
            } else if (relation === 'siblings') {
                return $node.closest('table').parent().siblings().find('.node:first');
            } else {
                return $();
            }
        },
        hideParentEnd: function (event) {
            $(event.target).removeClass('sliding');
            event.data.upperLevel.addClass('hidden').slice(1).removeAttr('style');
        },
        // recursively hide the ancestor node and sibling nodes of the specified node
        hideParent: function ($node) {
            var $upperLevel = $node.closest('.nodes').siblings();
            if ($upperLevel.eq(0).find('.spinner').length) {
                $node.closest('.orgchart').data('inAjax', false);
            }
            // hide the sibling nodes
            if (this.getNodeState($node, 'siblings').visible) {
                this.hideSiblings($node);
            }
            // hide the lines
            var $lines = $upperLevel.slice(1);
            $lines.css('visibility', 'hidden');
            // hide the superior nodes with transition
            var $parent = $upperLevel.eq(0).find('.node');
            if (this.getNodeState($parent).visible) {
                $parent.addClass('sliding slide-down').one('transitionend', { 'upperLevel': $upperLevel }, this.hideParentEnd);
            }
            // if the current node has the parent node, hide it recursively
            if (this.getNodeState($parent, 'parent').visible) {
                this.hideParent($parent);
            }
        },
        showParentEnd: function (event) {
            var $node = event.data.node;
            $(event.target).removeClass('sliding');
            if (this.isInAction($node)) {
                this.switchVerticalArrow($node.children('.topEdge'));
            }
        },
        // show the parent node of the specified node
        showParent: function ($node) {
            // just show only one superior level
            var $upperLevel = $node.closest('.nodes').siblings().removeClass('hidden');
            // just show only one line
            $upperLevel.eq(2).children().slice(1, -1).addClass('hidden');
            // show parent node with animation
            var $parent = $upperLevel.eq(0).find('.node');
            this.repaint($parent[0]);
            $parent.addClass('sliding').removeClass('slide-down').one('transitionend', { 'node': $node }, this.showParentEnd.bind(this));
        },
        stopAjax: function ($nodeLevel) {
            if ($nodeLevel.find('.spinner').length) {
                $nodeLevel.closest('.orgchart').data('inAjax', false);
            }
        },
        isVisibleNode: function (index, elem) {
            return this.getNodeState($(elem)).visible;
        },
        //
        hideChildrenEnd: function (event) {
            var $node = event.data.node;
            event.data.animatedNodes.removeClass('sliding');
            if (event.data.isVerticalDesc) {
                event.data.lowerLevel.addClass('hidden');
            } else {
                event.data.animatedNodes.closest('.nodes').prevAll('.lines').removeAttr('style').addBack().addClass('hidden');
                event.data.lowerLevel.last().find('.verticalNodes').addClass('hidden');
            }
            if (this.isInAction($node)) {
                this.switchVerticalArrow($node.children('.bottomEdge'));
            }
        },
        // recursively hide the descendant nodes of the specified node
        hideChildren: function ($node) {
            var $lowerLevel = $node.closest('tr').siblings();
            this.stopAjax($lowerLevel.last());
            var $animatedNodes = $lowerLevel.last().find('.node').filter(this.isVisibleNode.bind(this));
            var isVerticalDesc = $lowerLevel.last().is('.verticalNodes') ? true : false;
            if (!isVerticalDesc) {
                $animatedNodes.closest('table').closest('tr').prevAll('.lines').css('visibility', 'hidden');
            }
            this.repaint($animatedNodes.get(0));
            $animatedNodes.addClass('sliding slide-up').eq(0).one('transitionend', { 'animatedNodes': $animatedNodes, 'lowerLevel': $lowerLevel, 'isVerticalDesc': isVerticalDesc, 'node': $node }, this.hideChildrenEnd.bind(this));
        },
        //
        showChildrenEnd: function (event) {
            var $node = event.data.node;
            event.data.animatedNodes.removeClass('sliding');
            if (this.isInAction($node)) {
                this.switchVerticalArrow($node.children('.bottomEdge'));
            }
        },
        // show the children nodes of the specified node
        showChildren: function ($node) {
            var that = this;
            var $levels = $node.closest('tr').siblings();
            var isVerticalDesc = $levels.is('.verticalNodes') ? true : false;
            var $animatedNodes = isVerticalDesc
                ? $levels.removeClass('hidden').find('.node').filter(this.isVisibleNode.bind(this))
                : $levels.removeClass('hidden').eq(2).children().find('.node:first').filter(this.isVisibleNode.bind(this));
            // the two following statements are used to enforce browser to repaint
            this.repaint($animatedNodes.get(0));
            $animatedNodes.addClass('sliding').removeClass('slide-up').eq(0).one('transitionend', { 'node': $node, 'animatedNodes': $animatedNodes }, this.showChildrenEnd.bind(this));
        },
        //
        hideSiblingsEnd: function (event) {
            var $node = event.data.node;
            var $nodeContainer = event.data.nodeContainer;
            var direction = event.data.direction;
            event.data.lines.removeAttr('style');
            var $siblings = direction ? (direction === 'left' ? $nodeContainer.prevAll(':not(.hidden)') : $nodeContainer.nextAll(':not(.hidden)')) : $nodeContainer.siblings();
            $nodeContainer.closest('.nodes').prev().children(':not(.hidden)')
                .slice(1, direction ? $siblings.length * 2 + 1 : -1).addClass('hidden');
            event.data.animatedNodes.removeClass('sliding');
            $siblings.find('.node:gt(0)').filter(this.isVisibleNode.bind(this))
                .removeClass('slide-left slide-right').addClass('slide-up');
            $siblings.find('.lines, .nodes, .verticalNodes').addClass('hidden')
                .end().addClass('hidden');

            if (this.isInAction($node)) {
                this.switchHorizontalArrow($node);
            }
        },
        // hide the sibling nodes of the specified node
        hideSiblings: function ($node, direction) {
            var that = this;
            var $nodeContainer = $node.closest('table').parent();
            if ($nodeContainer.siblings().find('.spinner').length) {
                $node.closest('.orgchart').data('inAjax', false);
            }
            if (direction) {
                if (direction === 'left') {
                    $nodeContainer.prevAll().find('.node').filter(this.isVisibleNode.bind(this)).addClass('sliding slide-right');
                } else {
                    $nodeContainer.nextAll().find('.node').filter(this.isVisibleNode.bind(this)).addClass('sliding slide-left');
                }
            } else {
                $nodeContainer.prevAll().find('.node').filter(this.isVisibleNode.bind(this)).addClass('sliding slide-right');
                $nodeContainer.nextAll().find('.node').filter(this.isVisibleNode.bind(this)).addClass('sliding slide-left');
            }
            var $animatedNodes = $nodeContainer.siblings().find('.sliding');
            var $lines = $animatedNodes.closest('.nodes').prevAll('.lines').css('visibility', 'hidden');
            $animatedNodes.eq(0).one('transitionend', { 'node': $node, 'nodeContainer': $nodeContainer, 'direction': direction, 'animatedNodes': $animatedNodes, 'lines': $lines }, this.hideSiblingsEnd.bind(this));
        },
        //
        showSiblingsEnd: function (event) {
            var $node = event.data.node;
            event.data.visibleNodes.removeClass('sliding');
            if (this.isInAction($node)) {
                this.switchHorizontalArrow($node);
                $node.children('.topEdge').removeClass('fa-chevron-up').addClass('fa-chevron-down');
            }
        },
        //
        showRelatedParentEnd: function(event) {
            $(event.target).removeClass('sliding');
        },
        // show the sibling nodes of the specified node
        showSiblings: function ($node, direction) {
            var that = this;
            // firstly, show the sibling td tags
            var $siblings = $();
            if (direction) {
                if (direction === 'left') {
                    $siblings = $node.closest('table').parent().prevAll().removeClass('hidden');
                } else {
                    $siblings = $node.closest('table').parent().nextAll().removeClass('hidden');
                }
            } else {
                $siblings = $node.closest('table').parent().siblings().removeClass('hidden');
            }
            // secondly, show the lines
            var $upperLevel = $node.closest('table').closest('tr').siblings();
            if (direction) {
                $upperLevel.eq(2).children('.hidden').slice(0, $siblings.length * 2).removeClass('hidden');
            } else {
                $upperLevel.eq(2).children('.hidden').removeClass('hidden');
            }
            // thirdly, do some cleaning stuff
            if (!this.getNodeState($node, 'parent').visible) {
                $upperLevel.removeClass('hidden');
                var parent = $upperLevel.find('.node')[0];
                this.repaint(parent);
                $(parent).addClass('sliding').removeClass('slide-down').one('transitionend', this.showRelatedParentEnd);
            }
            // lastly, show the sibling nodes with animation
            var $visibleNodes = $siblings.find('.node').filter(this.isVisibleNode.bind(this));
            this.repaint($visibleNodes.get(0));
            $visibleNodes.addClass('sliding').removeClass('slide-left slide-right');
            $visibleNodes.eq(0).one('transitionend', { 'node': $node, 'visibleNodes': $visibleNodes }, this.showSiblingsEnd.bind(this));
        },
        // start up loading status for requesting new nodes
        startLoading: function ($edge) {
            var $chart = this.$chart;
            if (typeof $chart.data('inAjax') !== 'undefined' && $chart.data('inAjax') === true) {
                return false;
            }

            $edge.addClass('hidden');
            $edge.parent().append('<i class="fa fa-circle-o-notch fa-spin spinner"></i>')
                .children().not('.spinner').css('opacity', 0.2);
            $chart.data('inAjax', true);
            $('.oc-export-btn' + (this.options.chartClass !== '' ? '.' + this.options.chartClass : '')).prop('disabled', true);
            return true;
        },
        // terminate loading status for requesting new nodes
        endLoading: function ($edge) {
            var $node = $edge.parent();
            $edge.removeClass('hidden');
            $node.find('.spinner').remove();
            $node.children().removeAttr('style');
            this.$chart.data('inAjax', false);
            $('.oc-export-btn' + (this.options.chartClass !== '' ? '.' + this.options.chartClass : '')).prop('disabled', false);
        },
        // whether the cursor is hovering over the node
        isInAction: function ($node) {
            return $node.children('.edge').attr('class').indexOf('fa-') > -1 ? true : false;
        },
        //
        switchVerticalArrow: function ($arrow) {
            $arrow.toggleClass('fa-chevron-up').toggleClass('fa-chevron-down');
        },
        //
        switchHorizontalArrow: function ($node) {
            var opts = this.options;
            if (opts.toggleSiblingsResp && (typeof opts.ajaxURL === 'undefined' || $node.closest('.nodes').data('siblingsLoaded'))) {
                var $prevSib = $node.closest('table').parent().prev();
                if ($prevSib.length) {
                    if ($prevSib.is('.hidden')) {
                        $node.children('.leftEdge').addClass('fa-chevron-left').removeClass('fa-chevron-right');
                    } else {
                        $node.children('.leftEdge').addClass('fa-chevron-right').removeClass('fa-chevron-left');
                    }
                }
                var $nextSib = $node.closest('table').parent().next();
                if ($nextSib.length) {
                    if ($nextSib.is('.hidden')) {
                        $node.children('.rightEdge').addClass('fa-chevron-right').removeClass('fa-chevron-left');
                    } else {
                        $node.children('.rightEdge').addClass('fa-chevron-left').removeClass('fa-chevron-right');
                    }
                }
            } else {
                var $sibs = $node.closest('table').parent().siblings();
                var sibsVisible = $sibs.length ? !$sibs.is('.hidden') : false;
                $node.children('.leftEdge').toggleClass('fa-chevron-right', sibsVisible).toggleClass('fa-chevron-left', !sibsVisible);
                $node.children('.rightEdge').toggleClass('fa-chevron-left', sibsVisible).toggleClass('fa-chevron-right', !sibsVisible);
            }
        },
        //
        repaint: function (node) {
            if (node) {
                node.style.offsetWidth = node.offsetWidth;
            }
        },
        //
        nodeEnterLeaveHandler: function (event) {
            var $node = $(event.delegateTarget), flag = false;
            var $topEdge = $node.children('.topEdge');
            var $rightEdge = $node.children('.rightEdge');
            var $bottomEdge = $node.children('.bottomEdge');
            var $leftEdge = $node.children('.leftEdge');
            if (event.type === 'mouseenter') {
                if ($topEdge.length) {
                    flag = this.getNodeState($node, 'parent').visible;
                    $topEdge.toggleClass('fa-chevron-up', !flag).toggleClass('fa-chevron-down', flag);
                }
                if ($bottomEdge.length) {
                    flag = this.getNodeState($node, 'children').visible;
                    $bottomEdge.toggleClass('fa-chevron-down', !flag).toggleClass('fa-chevron-up', flag);
                }
                if ($leftEdge.length) {
                    this.switchHorizontalArrow($node);
                }
            } else {
                $node.children('.edge').removeClass('fa-chevron-up fa-chevron-down fa-chevron-right fa-chevron-left');
            }
        },
        //
        nodeClickHandler: function (event) {
            this.$chart.find('.focused').removeClass('focused');
            $(event.delegateTarget).addClass('focused');
        },
        // load new nodes by ajax
        loadNodes: function (rel, url, $edge) {
            var that = this;
            var opts = this.options;

            var idP =localStorage.getItem("idPersona");
            if (idP==null){idP="undefined"}
            $.ajax({ 'url':"inc/funciones.php?funcion=SelectChildrens&id="+idP, 'dataType': 'json','type':'GET' })
                .done(function (data) {

                    $(".divTransversal"+idP).css("display","flex");
                    console.log("orgChart: " + data);
                    if(data.children != null) {

                        if (that.$chart.data('inAjax')) {
                            if (rel === 'parent') {
                                if (!$.isEmptyObject(data)) {
                                    that.addParent($edge.parent(), data);
                                }
                            } else if (rel === 'children') {
                                if (data.children.length) {
                                    that.addChildren($edge.parent(), data[rel]);
                                }
                            } else {
                                that.addSiblings($edge.parent(), data.siblings ? data.siblings : data);
                            }
                        }

                    }


                    function toTitleCase(str) {
                        return str.replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();});
                    }
                    setTimeout(function(){

                        $( ".node" ).each(function()
                        {
                            var cont = $(this).children("div.title").html();
                            if(cont == null ){cont= "0;0;0;0;0"}
                            var datos = cont.split(";");
                            var cargo = $(this).children("div.content").html();
                            var btnDown2= $(this).find(".bottomEdge");
                            $(this).attr("data-id",datos[5])


                            if(cargo.length > 18){
                                var res1 = cargo;
                                var res2 = "</div><div style='height:14px;font-size: 14px;color: rgba(0, 0, 0, 0.6);word-wrap: break-word;white-space: pre-wrap;'></div>"

                                var  res =res1+res2

                                res=res.toLowerCase();
                                res = res=res.substr(0,1).toUpperCase()+res.substr(1);
                            }
                            else
                            {
                                var res= cargo;
                                res=res.toLowerCase();
                                res = res=res.substr(0,1).toUpperCase()+res.substr(1);
                            }


                            var icon="";
                            if(btnDown2.length == 0 ){icon='<i class="edge verticalEdge bottomEdge fa" style="position:relative"></i>';}

                            if($(this).hasClass("node2"))
                            {}
                            else {


                                var nombre = datos[0].split(" ");



                                var nombreDef= toTitleCase(datos[0]);
                                nombreDef = "<strong>"+nombreDef+"</strong>";

                                res=res.toLowerCase();
                                res = res=res.substr(0,1).toUpperCase()+res.substr(1);

                                res = res.replace("&Amp;","")







                                if (datos[2] === "true")
                                {

                                    var img = datos[1].split("/");
                                    datos[1]  = "http://intranet.carsa.com.py/wp-content/themes/sydney/organigrama/img/fotos/192.168.16.116:8081/"+img[(img.length-1)];

                                    $(this).html("");
                                    $(this).removeClass("node node2")
                                    $(this).addClass( "ghost" );
                                    $(this).attr("data-id",datos[5])

                                    var html = '<div style="width:50%;border-right: 3px solid #aaa;display: flex;">'
                                        +'<div style="width: 50%;"></div>'
                                        +'<div style="width: 50%;display: flex; ">'
                                        +'<div style="width: 63%;margin-left: 200px;">'
                                        +'<div class="node node2" data-id="'+datos[5]+'" style="float: right;"><div class="content background-border" style="height: 100%;border-radius: 13px;background: #fffcfc;border: 2px dashed #c59d4c;">'
//                                        +'<div class="divSup infoContent background-theme" style="background:#aaa" data-json="'+datos[4]+'" data-img="'+datos[1]+'" data-name="'+datos[0]+'" data-anti="'+datos[6]+'" data-fullname="'+datos[7]+'" data-cargo="'+jsUcfirst(cargo)+'">'
                                        +'<div class="divSup infoContent background-theme" style="background:#aaa" data-json="'+datos[4]+'" data-img="'+datos[1]+'" data-name="'+datos[0]+'" data-anti="'+datos[6]+'" data-fullname="'+datos[7]+'" data-cargo="'+jsUcfirst(cargo)+'" data-codFunc="'+datos[5]+'">'
                                        +'<img src="'+datos[1]+'" alt="" height=61 width=65 class="img">'
                                        +'</div>'
                                        +'<div class="divInfer">'
                                        +'<div class="" style="height: 39%;"></div>'
                                        +'<div class="" style="height: 65%;">'
                                        +'<div style="height: 14px;"><spam class="name">'+nombreDef+'</spam></div><div style="height:14px; word-wrap:break-word; white-space:pre-wrap; text-transform:capitalize;" class="name">'+res+'</div>'
                                        +'</div></div></div></div></div></div>'
                                        +'<div style="width:30%;float:right;">'
                                        +'<br><br><div style="height: 2px; border-top: 6px #aaa; border-top-style: dashed;"></div></div></div></div> <div style="width:50%;border-left: 3px solid #aaa;"></div>';

                                    $(this).html(html);


                                }
                                else
                                {

                                    if (datos[3] != "")
                                    {

                                        var htmlJump= '<div style="display: flex;  height: '+datos[3]+'px;"><did style="background: white;height: 100%;  width: 50%;border-right: 3px solid #aaaaaa;"></did>  <div style="background: white;height: 100%;  width: 50%;  border-left: 3px solid #aaaaaa;"> </div></div>';


                                        $(this).addClass( "node2" );
                                        $(this).html("");
                                        $(this).attr("data-id",datos[5])

                                        var img = datos[1].split("/");
                                        datos[1]  = "http://intranet.carsa.com.py/wp-content/themes/sydney/organigrama/img/fotos/192.168.16.116:8081/"+img[(img.length-1)];


                                        var html ='<div class="content background-border" style="height: 100%;border-radius: 13px;background: #fffcfc;border: 2px solid #c59d4c;">'
//                                            +'<div class="divSup infoContent background-theme" data-json="'+datos[4]+'" data-img="'+datos[1]+'" data-name="'+datos[0]+'" data-anti="'+datos[6]+'" data-fullname="'+datos[7]+'" data-cargo="'+jsUcfirst(cargo)+'">'
                                            +'<div class="divSup infoContent background-theme" data-json="'+datos[4]+'" data-img="'+datos[1]+'" data-name="'+datos[0]+'" data-anti="'+datos[6]+'" data-fullname="'+datos[7]+'" data-cargo="'+jsUcfirst(cargo)+'" data-codFunc="'+datos[5]+'">'
                                            +'<img src="'+datos[1]+'" alt=""  height=61 width=65 class="img">'
                                            +'</div>'
                                            +'<div class="divInfer">'
                                            +'<div class="" style="height: 39%;"></div>'
                                            +'<div class="" style="height: 65%;">'
                                            +'<div style="height: 14px;"><spam class="name">'+nombreDef+'</spam></div><div style="height:14px; word-wrap:break-word; white-space:pre-wrap; text-transform:capitalize;" class="name">'+res+'</div>'
                                            +'</div></div></div>'+icon+'</div>';

                                        $(this).parent().prepend(htmlJump);
                                        $(this).html(html);

                                    }


                                    else
                                    {
                                        var img = datos[1].split("/");
                                        datos[1]  = "http://intranet.carsa.com.py/wp-content/themes/sydney/organigrama/img/fotos/192.168.16.116:8081/"+img[(img.length-1)];


                                        $(this).addClass( "node2" );
                                        $(this).html("");
                                        $(this).attr("data-id",datos[5])
                                        var html ='<div class="content" style="height: 100%;border-radius: 13px;background: #fffcfc;border: 2px solid #c59d4c;">'
//                                            +'<div class="divSup infoContent background-theme" data-json="'+datos[4]+'" data-img="'+datos[1]+'" data-name="'+datos[0]+'" data-anti="'+datos[6]+'" data-fullname="'+datos[7]+'" data-cargo="'+jsUcfirst(cargo)+'">'
                                            +'<div class="divSup infoContent background-theme" data-json="'+datos[4]+'" data-img="'+datos[1]+'" data-name="'+datos[0]+'" data-anti="'+datos[6]+'" data-fullname="'+datos[7]+'" data-cargo="'+jsUcfirst(cargo)+'" data-codFunc="'+datos[5]+'">'
                                            +'<img src="'+datos[1]+'" alt="" height=61 width=65 class="img">'
                                            +'</div>'
                                            +'<div class="divInfer">'
                                            +'<div class="" style="height: 39%;"></div>'
                                            +'<div class="" style="height: 65%;">'
                                            +'<div style="height: 14px;"><spam class="name">'+nombreDef+'</spam></div><div style="height:14px; word-wrap:break-word; white-space:pre-wrap; text-transform:capitalize;" class="name">'+res+'</div>'
                                            +'</div></div></div>'+icon+'</div>';
                                        $(this).html(html);


                                    }
                                }
                            }

                        });

                        $("#infoedad").html("");
                        $("#cant_depen").html("");
                        $("#cant_cont").html("");
                        $("#vivienda").html("");
                        $("#movilidad").html("");
                        $("#qbackup").html("");
                        $("#dbackup").html("");
                        $("#infogrado").html("");
                        $("#infoNroDocumento").html("");
                        $("#infoGerencia").html("");
                        $("#infoOrganigrama").html("");
                        $("#infoSuperior").html("");
                        $("#infoFechaIngreso").html("");





                        $.ajax(
                            {
                                url:"inc/funciones.php?funcion=selectTransversales",
                                success: function(result)
                                {
                                    $("#miniloading").fadeOut();
                                    console.log("HOOLOX");
                                    if(result != null)
                                    {
                                        $("#infogrado").html("");
                                        $("#infoNroDocumento").html("");
                                        $("#infoGerencia").html("");
                                        $("#infoOrganigrama").html("");
                                        $("#infoSuperior").html("");
                                        $("#infoFechaIngreso").html("");
                                        $("#infouni").html("");
                                        $("#infomasterado").html("");
                                        $("#infodiplomado").html("");


                                        $("#infoedad").html("");
                                        $("#cant_depen").html("");
                                        $("#cant_cont").html("");
                                        $("#vivienda").html("");
                                        $("#movilidad").html("");
                                        $("#qbackup").html("");
                                        $("#dbackup").html("");




                                        if(result.informacion != null)
                                        {
                                            $("#infogrado").html(result.informacion[0].GRADO_ACADEMICO);
                                            $("#infouni").html(result.informacion[0].UNIVERSIDAD);
                                            $("#infomasterado").html(result.informacion[0].MASTERADO);
                                            $("#infodiplomado").html(result.informacion[0].DIPLOMADO);
                                            $("#infoedad").html(result.informacion[0].EDAD);
                                            $("#cant_depen").html(result.informacion[0].CAN_PER_DEP_ECO);
                                            $("#cant_cont").html(result.informacion[0].CAN_CONT_GASTOS);
                                            $("#vivienda").html(result.informacion[0].TIPO_VIVIENDA);
                                            $("#movilidad").html(result.informacion[0].MOV_PROPIA);
                                            $("#infoNroDocumento").html(result.informacion[0].NRO_CEDULA);
                                            $("#infoGerencia").html(jsUcfirst(result.informacion[0].GERENCIA));
                                            $("#infoFechaIngreso").html(result.informacion[0].FECHA_INGRESO);

                                            var nomGer = "'"+result.informacion[0].GERENCIA+"'";
                                            var verOrganigrama = '<button type="button" onclick="getOrganigrama(' + result.informacion[0].COD_GERENCIA + ', '+ nomGer +');">Ver</button>';
                                            $("#infoOrganigrama").html(verOrganigrama);

                                            if (result.informacion[0].SUPERIOR_INMEDIATO != ''){
                                                document.getElementById("titSuperior").style.display = "";
                                                document.getElementById("saltSuperior").style.display = "";
                                                document.getElementById("infoSuperior").style.display = "";
                                                $("#titSuperior").html('Superior: ');
                                                $("#infoSuperior").html(jsUcfirst(result.informacion[0].SUPERIOR_INMEDIATO));
                                            } else {
                                                document.getElementById("titSuperior").style.display = "none";
                                                document.getElementById("saltSuperior").style.display = "none";
                                                document.getElementById("infoSuperior").style.display = "none";
                                                $("#titSuperior").html('');
                                                $("#infoSuperior").html('');
                                            }
                                        }

                                        if(result.dependencia != null)
                                        {
                                            $(".tabladependencia").fadeIn();
                                            $("#datosdependencia").fadeIn();
                                            for (var i = 0; i < result.dependencia.length; i++)
                                            {


                                                var relacion = result.dependencia[i].PARENTESCO;
                                                var nombre = result.dependencia[i].NOMBRE_COMPLETO_DEP;
                                                var html='<tr class="tr"><td class="td">'+relacion+'</td><td class="td">'+nombre+'</td></tr>';
                                                $(".tabladependencia").append(html);
                                                //alert(nombre);


                                            }

                                        }
                                        else  {

                                            $(".tabladependencia").fadeOut()
                                            $("#datosdependencia").fadeOut()
                                        }

                                        if(result.academico != null) {
                                            document.getElementById("titinfogrado").style.display = "";

                                            for (var i = 0; i < result.academico.length; i++){
                                                var cursado = result.academico[i].ANTECEDENTE_ACADEMICO;
                                                cursado     = cursado.replace("/", " / ");
                                                cursado     = cursado.replace("/culminado", " / culminado");
                                                cursado     = cursado.replace("/en", " / en");
                                                cursado     = cursado.replace("/proceso", " / proceso");
                                                var html    = '<spam style="text-transform:capitalize;">'+cursado+'</spam><br>';
                                                $("#infogrado").append(html);
                                            }
                                        } else {
                                            document.getElementById("titinfogrado").style.display = "none";
                                        }

                                        if(result.hobbies != null) {
                                            for (var i = 0; i < result.hobbies.length; i++)  {
                                                var hobbie = result.hobbies[i].HOBBIE;
                                                var OBSERVACION = result.hobbies[i].OBSERVACION;
                                                var html='<tr class="tr"><td class="td">'+hobbie+'</td><td class="td">'+OBSERVACION+'</td></tr>';
                                                $(".tablahobbies").append(html);
                                                $(".tablahobbies").show();
                                            }
                                        } else {
                                            $(".tablahobbies").hide();
                                        }


                                        if(result.backups != null ) {
                                            document.getElementById("titqbackup").style.display = "none";
                                            document.getElementById("titdbackup").style.display = "none";

                                            for (var i = 0; i < result.backups.length; i++) {
                                                if(result.backups[i].TIPO == "1") {
                                                    document.getElementById("titqbackup").style.display = "";
                                                    $("#qbackup").append('<spam>-'+toTitleCase(result.backups[i].NOMBRE_BACKUP)+'</spam><br>');
                                                } else {
                                                    document.getElementById("titdbackup").style.display = "";
                                                    $("#dbackup").append('<spam>-'+toTitleCase(result.backups[i].NOMBRE_BACKUP)+'</spam><br>');
                                                }
                                            }
                                        } else {
                                            document.getElementById("titqbackup").style.display = "none";
                                            document.getElementById("titdbackup").style.display = "none";
                                        }

                                        
                                        var ultnivel="";
                                        var ultid="";
                                        var ultsuper="";

                                        for (var i = 0; i < result.length; i++)
                                        {
                                            var datos=result[i].name.split(";")
                                            if($("#"+result[i].superior).hasClass("transversal"+result[i].id))
                                            {}
                                            else {
                                                var nombre = datos[0].split(" ");

                                                var cargo = jsUcfirst(result[i].title);

                                                var parent = result[i].superior;
                                                var display ="";


                                                if(parent == localStorage.getItem("idPersona"))
                                                {
                                                    display="flex"
                                                }
                                                else
                                                {
                                                    display="none"
                                                }

                                                if(cargo.length > 18)
                                                {
                                                    var res1 = cargo;
                                                    var res2 = "</div><div style='height:14px;font-size: 14px;color: rgba(0, 0, 0, 0.6);word-wrap: break-word;white-space: pre-wrap;'></div>"

                                                    var  res =res1+res2
                                                    res=res.toLowerCase();
                                                    res = res=res.substr(0,1).toUpperCase()+res.substr(1);

                                                }
                                                else
                                                {
                                                    var res= cargo;
                                                    res=res.toLowerCase();
                                                    res = res=res.substr(0,1).toUpperCase()+res.substr(1);
                                                    res=res+"</div>";
                                                }

                                                var nombreDef= toTitleCase(datos[0]);
                                                nombreDef = "<strong>"+nombreDef+"</strong>";

                                                if(ultnivel == result[i].nivel && ultsuper == result[i].superior)
                                                {

                                                    if(result[i].posicion=="DERECHA")
                                                    {
                                                        var img = datos[1].split("/");
                                                        datos[1]  = "http://intranet.carsa.com.py/wp-content/themes/sydney/organigrama/img/fotos/192.168.16.116:8081/"+img[(img.length-1)];


                                                        var html = ""
                                                            +'<div style="display:flex"><div style="width: 10%;">'
                                                            +'<div style="height: 50%;  width: 100%;border-bottom: 3px solid #aaaaaa;">'
                                                            +'</div> <div style="  height: 50%;  width: 100%;border-top: 3px solid #aaaaaa;">'
                                                            +'</div>'
                                                            +'</div>'
                                                            +'<div style="display:flex"><div style="width:90%">'
                                                            +'<div id="'+result[i].id+'" class="node node2" data-id="'+result[i].id+'" style="float:left;">'
                                                            +'<div class="content background-border">'
//                                                            +'<div class="divSup infoContent background-theme" data-json="" data-img="'+datos[1]+'" data-name="'+datos[0]+'" data-anti="'+datos[6]+'" data-fullname="'+datos[7]+'" data-cargo="'+jsUcfirst(result[i].title)+'">'
                                                            +'<div class="divSup infoContent background-theme" data-json="" data-img="'+datos[1]+'" data-name="'+datos[0]+'" data-anti="'+datos[6]+'" data-fullname="'+datos[7]+'" data-cargo="'+jsUcfirst(result[i].title)+'" data-codFunc="'+result[i].id+'">'
                                                            +'<img src="'+datos[1]+'" alt="" height="61" width="65" class="img">'
                                                            +'</div>'
                                                            +'<div class="divInfer">'
                                                            +'<div class="" style="height: 39%;">'
                                                            +'</div>'
                                                            +'<div class="" style="height: 65%;">'
                                                            +'<div style="height: 14px;">'
                                                            +'<spam class="name">'
                                                            +'<strong>'+nombreDef+'</strong>'
                                                            +'</spam>   </div>'
                                                            +'<div style="height:14px" class="name">'
                                                            +res
                                                            +'<div style="height:14px;font-size: 14px;color: Rgba(0, 0, 0, 0.6)">'
                                                            +'</div></div></div></div>'
                                                            +'</div>'
                                                            +'</div>'
                                                            +'</div> </div> </div>';

                                                        $("#divderecha"+ultid).append(html);
                                                        $("#"+result[i].superior).addClass("transversal"+result[i].id);



                                                    }
                                                    else
                                                    {
                                                        var img = datos[1].split("/");
                                                        datos[1]  = "http://intranet.carsa.com.py/wp-content/themes/sydney/organigrama/img/fotos/192.168.16.116:8081/"+img[(img.length-1)];


                                                        var html =""
                                                            +'<div style="display:flex"><div style="display:flex"><div style="width: 90%;">'
                                                            +'<div id="'+result[i].id+'" class="node node2" data-id="'+result[i].id+'" style="float: right;">'
                                                            +'<div class="content background-border">'
//                                                            +'<div class="divSup infoContent background-theme" data-json="TMASI" data-img="'+datos[1]+'" data-name="'+datos[0]+'" data-anti="'+datos[6]+'" data-fullname="'+datos[7]+'" data-cargo="'+jsUcfirst(result[i].title)+'">'
                                                            +'<div class="divSup infoContent background-theme" data-json="TMASI" data-img="'+datos[1]+'" data-name="'+datos[0]+'" data-anti="'+datos[6]+'" data-fullname="'+datos[7]+'" data-cargo="'+jsUcfirst(result[i].title)+'" data-codFunc="'+result[i].id+'">'
                                                            +'<img src="'+datos[1]+'" alt="" height="61" width="65" class="img">'
                                                            +'</div>'
                                                            +'<div class="divInfer">'
                                                            +'<div class="" style="height: 39%;">'
                                                            +'</div>'
                                                            +'<div class="" style="height: 65%;">'
                                                            +'<div style="height: 14px;">'
                                                            +'<spam class="name">'
                                                            +'<strong>'+nombreDef+'</strong>'
                                                            +'</spam>   </div>'
                                                            +'<div style="height:14px" class="name">'
                                                            +res
                                                            +'<div style="height:14px;font-size: 14px;color: Rgba(0, 0, 0, 0.6)">'
                                                            +'</div></div></div></div>'
                                                            +'</div></div>'
                                                            +'<div style="width:10%"><div style="height: 50%;  width: 100%;border-bottom: 3px solid #aaaaaa;">'
                                                            +'</div> <div style="  height: 50%;  width: 100%;border-top: 3px solid #aaaaaa;">'
                                                            +'</div></div></div></div>';

                                                        $("#divizquierda"+ultid).append(html);
                                                        $("#"+result[i].superior).addClass("transversal"+result[i].id);


                                                    }




                                                }
                                                else
                                                {





                                                    if(result[i].posicion=="DERECHA")
                                                    {


                                                        var img = datos[1].split("/");
                                                        datos[1]  = "http://intranet.carsa.com.py/wp-content/themes/sydney/organigrama/img/fotos/192.168.16.116:8081/"+img[(img.length-1)];

                                                        var html =
                                                            '<div id="divTransversal'+result[i].superior+'" class="divTransversal'+result[i].superior+'" style="display:'+display+';/* display: none; */">'
                                                            +'<div id="divizquierda'+result[i].id+'" style="width:50%;background: #ffffff;display: flex;border-right: 3px solid #aaaaaa;">'
                                                            +'</div><div id="divderecha'+result[i].id+'"  style="width:50%;background:#ffffff;display: flex;border-left: 3px solid #aaaaaa;">'
                                                            +'<div style="width: 10%;">'

                                                            +'<div style="height: 50%;  width: 100%;border-bottom: 3px solid #aaaaaa;">'
                                                            +'</div> <div style="  height: 50%;  width: 100%;border-top: 3px solid #aaaaaa;">'
                                                            +'</div>'
                                                            +'</div>'
                                                            +'<div style="width:90%">'
                                                            +'<div id="'+result[i].id+'" class="node node2" data-id="'+result[i].id+'" style="float:left;">'
                                                            +'<div class="content background-border">'
//                                                            +'<div class="divSup infoContent background-theme" data-json="" data-img="'+datos[1]+'" data-name="'+datos[0]+'" data-anti="'+datos[6]+'" data-fullname="'+datos[7]+'" data-cargo="'+jsUcfirst(result[i].title)+'">'
                                                            +'<div class="divSup infoContent background-theme" data-json="" data-img="'+datos[1]+'" data-name="'+datos[0]+'" data-anti="'+datos[6]+'" data-fullname="'+datos[7]+'" data-cargo="'+jsUcfirst(result[i].title)+'" data-codFunc="'+result[i].id+'">'
                                                            +'<img src="'+datos[1]+'" alt="" height="61" width="65" class="img">'
                                                            +'</div>'
                                                            +'<div class="divInfer">'
                                                            +'<div class="" style="height: 39%;">'
                                                            +'</div>'
                                                            +'<div class="" style="height: 65%;">'
                                                            +'<div style="height: 14px;">'
                                                            +'<spam class="name">'
                                                            +'<strong>'+nombreDef+'</strong>'
                                                            +'</spam>   </div>'
                                                            +'<div style="height:14px" class="name">'
                                                            +res
                                                            +'<div style="height:14px;font-size: 14px;color: Rgba(0, 0, 0, 0.6)">'
                                                            +'</div></div></div></div>'
                                                            +'</div>'
                                                            +'</div>'
                                                            +'</div> </div>';
                                                    }
                                                    else
                                                    {

                                                        var img = datos[1].split("/");
                                                        datos[1]  = "http://intranet.carsa.com.py/wp-content/themes/sydney/organigrama/img/fotos/192.168.16.116:8081/"+img[(img.length-1)];




                                                        var html =
                                                            '<div id="divTransversal'+result[i].superior+'" class="divTransversal'+result[i].superior+'" style="display:'+display+';/* display: none; */">'
                                                            +'<div id="divizquierda'+result[i].id+'" style="width:50%;background: #ffffff;display: flex;border-right: 3px solid #aaaaaa;">'
                                                            +'<div style="width: 90%;">'
                                                            +'<div id="'+result[i].id+'" class="node node2" data-id="'+result[i].id+'" style="float: right;">'
                                                            +'<div class="content background-border">'
//                                                            +'<div class="divSup infoContent background-theme" data-json="TMASI" data-img="'+datos[1]+'" data-name="'+datos[0]+'" data-anti="'+datos[6]+'" data-fullname="'+datos[7]+'" data-cargo="'+jsUcfirst(result[i].title)+'">'
                                                            +'<div class="divSup infoContent background-theme" data-json="TMASI" data-img="'+datos[1]+'" data-name="'+datos[0]+'" data-anti="'+datos[6]+'" data-fullname="'+datos[7]+'" data-cargo="'+jsUcfirst(result[i].title)+'" data-codFunc="'+result[i].id+'">'
                                                            +'<img src="'+datos[1]+'" alt="" height="61" width="65" class="img">'
                                                            +'</div>'
                                                            +'<div class="divInfer">'
                                                            +'<div class="" style="height: 39%;">'
                                                            +'</div>'
                                                            +'<div class="" style="height: 65%;">'
                                                            +'<div style="height: 14px;">'
                                                            +'<spam class="name">'
                                                            +'<strong>'+nombreDef+'</strong>'
                                                            +'</spam>   </div>'
                                                            +'<div style="height:14px" class="name">'
                                                            +res
                                                            +'<div style="height:14px;font-size: 14px;color: Rgba(0, 0, 0, 0.6)">'
                                                            +'</div></div></div></div>'
                                                            +'</div></div>'
                                                            +'<div style="width:10%"><div style="height: 50%;  width: 100%;border-bottom: 3px solid #aaaaaa;">'
                                                            +'</div> <div style="  height: 50%;  width: 100%;border-top: 3px solid #aaaaaa;">'
                                                            +'</div></div></div><div id="divderecha'+result[i].id+'" style="width:50%;background: white;border-left: 3px solid #aaaaaa;">'
                                                            +'<br><br><br><br></div> </div>';

                                                    }


                                                    $("#"+result[i].superior).parent().append(html);
                                                    $("#"+result[i].superior).addClass("transversal"+result[i].id);

                                                }

                                                $("#"+result[i].id).hover(function() {
                                                    localStorage.setItem("idPersona",$(this).data("id"));
                                                    $(this).children().addClass('transition');

                                                }, function() {
                                                    $(".node2").children().removeClass('transition');
                                                });


                                                $("#"+result[i].id).on( "click", function() {

                                                    elements.addClass('active');

                                                    $(".tablaLogros").empty();
                                                    $(".tablaLogros").append('<thead style="background-color:#f5f5f5;"><tr class="tr"><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;"> Meses </td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;"> Tipo de Producción </td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;"> Meta </td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;"> Logrado </td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;"> Ratio </td></tr></thead>');
                                                    $("#infoedad").html("");
                                                    $("#cant_depen").html("");
                                                    $("#cant_cont").html("");
                                                    $("#vivienda").html("");
                                                    $("#movilidad").html("");
                                                    $("#qbackup").html("");
                                                    $("#dbackup").html("");
                                                    $("#infogrado").html("");
                                                    $("#infoNroDocumento").html("");
                                                    $("#infoGerencia").html("");
                                                    $("#infoOrganigrama").html("");
                                                    $("#infoSuperior").html("");
                                                    $("#infoFechaIngreso").html("");




                                                    $.ajax(
                                                        {
                                                            url:"inc/funciones.php?funcion=selectTablas&id="+localStorage.getItem("idPersona"),
                                                            success: function(result)
                                                            {
                                                                $("#infoedad").html("");
                                                                $("#cant_depen").html("");
                                                                $("#cant_cont").html("");
                                                                $("#vivienda").html("");
                                                                $("#movilidad").html("");
                                                                $("#qbackup").html("");
                                                                $("#dbackup").html("");
                                                                $("#miniloading").fadeOut();
                                                                $(".tablaMovimientos").empty();
                                                                $(".tablaSalario").empty();
                                                                $(".tablaLogros").empty();
                                                                $(".tablaEventos").empty();
                                                                $(".tablaDocumentos").empty();
                                                                $(".tablaCapacitaciones").empty();
                                                                $(".tablaAnotaciones").empty();
                                                                $(".tablaAntLaborales").empty();
                                                                $(".tabladependencia").empty();
                                                                $(".tablahobbies").empty();
                                                                $(".tabladependencia").append('<tr class="tr"><td class="td" style="color:black">Familiares Directos</td><td class="td" style="color:black">Nombre y Apellido </td> </tr>')
                                                                $(".tablahobbies").append('<tr class="tr"><td class="td" style="color:black">Hobbie</td><td class="td" style="color:black">Observaci&#243;n</td> </tr>')
                                                                $("#infogrado").html("");
                                                                $("#infoNroDocumento").html("");
                                                                $("#infoGerencia").html("");
                                                                $("#infoOrganigrama").html("");
                                                                $("#infoSuperior").html("");
                                                                $("#infoFechaIngreso").html("");
                                                                $("#infouni").html("");
                                                                $("#infomasterado").html("");
                                                                $("#infodiplomado").html("");





                                                                if(result.informacion != null)
                                                                {
                                                                    $("#infogrado").html(result.informacion[0].GRADO_ACADEMICO);
                                                                    $("#infouni").html(result.informacion[0].UNIVERSIDAD);
                                                                    $("#infomasterado").html(result.informacion[0].MASTERADO);
                                                                    $("#infodiplomado").html(result.informacion[0].DIPLOMADO); 
                                                                    $("#infoedad").html(result.informacion[0].EDAD);
                                                                    $("#cant_depen").html(result.informacion[0].CAN_PER_DEP_ECO);
                                                                    $("#cant_cont").html(result.informacion[0].CAN_CONT_GASTOS);
                                                                    $("#vivienda").html(result.informacion[0].TIPO_VIVIENDA);
                                                                    $("#movilidad").html(result.informacion[0].MOV_PROPIA);
                                                                    $("#infoNroDocumento").html(result.informacion[0].NRO_CEDULA);
                                                                    $("#infoGerencia").html(jsUcfirst(result.informacion[0].GERENCIA));
                                                                    $("#infoFechaIngreso").html(result.informacion[0].FECHA_INGRESO);

                                                                    var nomGer = "'"+result.informacion[0].GERENCIA+"'";
                                                                    var verOrganigrama = '<button type="button" onclick="getOrganigrama(' + result.informacion[0].COD_GERENCIA + ', '+ nomGer +');">Ver</button>';
                                                                    $("#infoOrganigrama").html(verOrganigrama);

                                                                    if (result.informacion[0].SUPERIOR_INMEDIATO != ''){
                                                                        document.getElementById("titSuperior").style.display = "";
                                                                        document.getElementById("saltSuperior").style.display = "";
                                                                        document.getElementById("infoSuperior").style.display = "";
                                                                        $("#titSuperior").html('Superior: ');
                                                                        $("#infoSuperior").html(jsUcfirst(result.informacion[0].SUPERIOR_INMEDIATO));
                                                                    } else {
                                                                        document.getElementById("titSuperior").style.display = "none";
                                                                        document.getElementById("saltSuperior").style.display = "none";
                                                                        document.getElementById("infoSuperior").style.display = "none";
                                                                        $("#titSuperior").html('');
                                                                        $("#infoSuperior").html('');
                                                                    }
                                                                }

                                                                if(result.dependencia != null)
                                                                {
                                                                    $(".tabladependencia").fadeIn();
                                                                    $("#datosdependencia").fadeIn();
                                                                    for (var i = 0; i < result.dependencia.length; i++)
                                                                    {


                                                                        var relacion = result.dependencia[i].PARENTESCO;
                                                                        var nombre = result.dependencia[i].NOMBRE_COMPLETO_DEP;
                                                                        var html='<tr class="tr"><td class="td">'+relacion+'</td><td class="td">'+nombre+'</td></tr>';
                                                                        $(".tabladependencia").append(html);
                                                                        //alert(nombre);


                                                                    }

                                                                }
                                                                else  {

                                                                    $(".tabladependencia").fadeOut()
                                                                    $("#datosdependencia").fadeOut()
                                                                }

                                                                if(result.academico != null) {
                                                                    document.getElementById("titinfogrado").style.display = "";

                                                                    for (var i = 0; i < result.academico.length; i++){
                                                                        var cursado = result.academico[i].ANTECEDENTE_ACADEMICO;
                                                                        cursado     = cursado.replace("/", " / ");
                                                                        cursado     = cursado.replace("/culminado", " / culminado");
                                                                        cursado     = cursado.replace("/en", " / en");
                                                                        cursado     = cursado.replace("/proceso", " / proceso");
                                                                        var html    = '<spam style="text-transform:capitalize;">'+cursado+'</spam><br>';
                                                                        $("#infogrado").append(html);
                                                                    }
                                                                } else {
                                                                    document.getElementById("titinfogrado").style.display = "none";
                                                                }


                                                                if(result.hobbies != null)
                                                                {

                                                                    for (var i = 0; i < result.hobbies.length; i++)
                                                                    {


                                                                        var hobbie = result.hobbies[i].HOBBIE;
                                                                        var OBSERVACION = result.hobbies[i].OBSERVACION;
                                                                        var html='<tr class="tr"><td class="td">'+hobbie+'</td><td class="td">'+OBSERVACION+'</td></tr>';
                                                                        $(".tablahobbies").show();

                                                                    }

                                                                }
                                                                else
                                                                {
                                                                    $(".tablahobbies").hide();
                                                                }


                                                                if(result.backups != null ) {
                                                                    document.getElementById("titqbackup").style.display = "none";
                                                                    document.getElementById("titdbackup").style.display = "none";

                                                                    for (var i = 0; i < result.backups.length; i++) {
                                                                        if(result.backups[i].TIPO == "1") {
                                                                            document.getElementById("titqbackup").style.display = "";
                                                                            $("#qbackup").append('<spam>-'+toTitleCase(result.backups[i].NOMBRE_BACKUP)+'</spam><br>');
                                                                        } else {
                                                                            document.getElementById("titdbackup").style.display = "";
                                                                            $("#dbackup").append('<spam>-'+toTitleCase(result.backups[i].NOMBRE_BACKUP)+'</spam><br>');
                                                                        }
                                                                    }
                                                                } else {
                                                                    document.getElementById("titqbackup").style.display = "none";
                                                                    document.getElementById("titdbackup").style.display = "none";
                                                                }
                                                                
                                                                var promedioLogrado = 0;
                                                                if (result.logros != null) {
                                                                    for (var i = 0; i < result.logros.length; i++) {
                                                                        var meta                = result.logros[i].META.split(".");
                                                                        var logro               = result.logros[i].LOGRADO.split(".");

                                                                        meta[0]                 = meta[0].toString().split('').reverse().join('').replace(/(?=\d*\,?)(\d{3})/g,'$1.');
                                                                        meta[0]                 = meta[0].split('').reverse().join('').replace(/^[\.]/,'');
                                                                        logro[0]                = logro[0].toString().split('').reverse().join('').replace(/(?=\d*\,?)(\d{3})/g,'$1.');
                                                                        logro[0]                = logro[0].split('').reverse().join('').replace(/^[\.]/,'');

                                                                        var color               = "";
                                                                        var texto               = "";

                                                                        result.logros[i].COLOR  = result.logros[i].COLOR.replace(/ /g , "")

                                                                        if (result.logros[i].COLOR == "ROJO") {
                                                                            color   = "red";
                                                                            texto   = "white";
                                                                        } else if (result.logros[i].COLOR == "VERDE") {
                                                                            color   = "green";
                                                                            texto   = "white";
                                                                        } else {
                                                                            color   = "#eae70a";
                                                                            texto   = "black";
                                                                        }

                                                                        if (i != (result.logros.length - 1)) {
                                                                            var html = '<tr class="tr"><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+result.logros[i].PERIODO+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+result.logros[i].TIPO+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+meta[0]+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+logro[0]+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke; background-color:'+result.logros[i].COLOR+'; color:'+result.logros[i].COLOR_TEXTO+'">'+result.logros[i].RATIO+'%</td></tr>';
                                                                        } else {
                                                                            var html = '<tr class="tr"><td class="td" style="text-align:center;">'+result.logros[i].PERIODO+'</td><td class="td" style="text-align:center;">'+result.logros[i].TIPO+'</td><td class="td" style="text-align:center;">'+meta[0]+'</td><td class="td" style="text-align:center;">'+logro[0]+'</td><td class="td" style="text-align:center; background-color:'+result.logros[i].COLOR+'; color:'+result.logros[i].COLOR_TEXTO+'">'+result.logros[i].RATIO+'%</td></tr>';
                                                                        }

                                                                        $(".tablaLogros").append(html);

                                                                        promedioLogrado = promedioLogrado + parseInt(result.logros[i].RATIO);
                                                                    }

                                                                    promedioLogrado = (promedioLogrado / result.logros.length);

                                                                    var htmlPromedio= '<tr class="tr" style="background-color:#f5f5f5;"><td class="td" colspan="3"></td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;"><strong>Promedio</strong></td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;"><strong>'+promedioLogrado.toFixed(2)+'%</strong></td></tr>';

                                                                    $(".tablaLogros").append(htmlPromedio);
                                                                } else {
                                                                    $(".tablaLogros").empty();
                                                                }

                                                                var fijo        = 0;
                                                                var variable    = 0;
                                                                var total       = 0
                                                                var aguinaldo   = 0;
                                                                var aporte      = 0;

                                                                if (result.salario != null) {
                                                                    $(".tablaSalario").empty();
                                                                    $(".tablaSalario").append('<thead style="background-color:#f5f5f5;"><tr class="tr"><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Meses</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Salario Variable(SV)</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Salario Fijo(SF)</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Salario Total(ST)</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Aguinaldo</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">IPS</td></tr></thead>');

                                                                    for (var i = 0; i < result.salario.length; i++) {
                                                                        fijo            = fijo + parseInt(result.salario[i].fijo);
                                                                        variable        = variable + parseInt(result.salario[i].variable);
                                                                        total           = total + parseInt(result.salario[i].total);
                                                                        aguinaldo       = aguinaldo + parseInt(result.salario[i].aguinaldo);
                                                                        aporte          = aporte + parseInt(result.salario[i].aporte);

                                                                        var VARIABLE    = result.salario[i].variable;
                                                                        var FIJO        = result.salario[i].fijo;
                                                                        var TOTAL       = result.salario[i].total;
                                                                        var AGUINALDO   = result.salario[i].aguinaldo;
                                                                        var APORTE      = result.salario[i].aporte;

                                                                        AGUINALDO       = AGUINALDO.toString().split('').reverse().join('').replace(/(?=\d*\,?)(\d{3})/g,'$1.');
                                                                        AGUINALDO       = AGUINALDO.split('').reverse().join('').replace(/^[\.]/,'');
                                                                        APORTE          = APORTE.toString().split('').reverse().join('').replace(/(?=\d*\,?)(\d{3})/g,'$1.');
                                                                        APORTE          = APORTE.split('').reverse().join('').replace(/^[\.]/,'');
                                                                        VARIABLE        = VARIABLE.toString().split('').reverse().join('').replace(/(?=\d*\,?)(\d{3})/g,'$1.');
                                                                        VARIABLE        = VARIABLE.split('').reverse().join('').replace(/^[\.]/,'');
                                                                        FIJO            = FIJO.toString().split('').reverse().join('').replace(/(?=\d*\,?)(\d{3})/g,'$1.');
                                                                        FIJO            = FIJO.split('').reverse().join('').replace(/^[\.]/,'');
                                                                        TOTAL           = TOTAL.toString().split('').reverse().join('').replace(/(?=\d*\,?)(\d{3})/g,'$1.');
                                                                        TOTAL           = TOTAL.split('').reverse().join('').replace(/^[\.]/,'');

                                                                        if (i != (result.salario.length - 1)) {
                                                                            var html = '<tr class="tr"> <td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+result.salario[i].periodo+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+VARIABLE+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+FIJO+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+TOTAL+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+AGUINALDO+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+APORTE+'</td></tr>';
                                                                        } else {
                                                                            var html = '<tr class="tr"> <td class="td" style="text-align:center;">'+result.salario[i].periodo+'</td><td class="td" style="text-align:center;">'+VARIABLE+'</td><td class="td" style="text-align:center;">'+FIJO+'</td><td class="td" style="text-align:center;">'+TOTAL+'</td><td class="td" style="text-align:center;">'+AGUINALDO+'</td><td class="td" style="text-align:center;">'+APORTE+'</td></tr>';
                                                                        }
                                                                        $(".tablaSalario").append(html);
                                                                    }

                                                                    total       = (total/result.salario.length);
                                                                    variable    = (variable/result.salario.length);
                                                                    fijo        = (fijo/result.salario.length);
                                                                    fijo        = fijo.toFixed(0);
                                                                    variable    = variable.toFixed(0);
                                                                    total       = total.toFixed(0);
                                                                    fijo        = fijo.toString().split('').reverse().join('').replace(/(?=\d*\,?)(\d{3})/g,'$1.');
                                                                    fijo        = fijo.split('').reverse().join('').replace(/^[\.]/,'');
                                                                    variable    = variable.toString().split('').reverse().join('').replace(/(?=\d*\,?)(\d{3})/g,'$1.');
                                                                    variable    = variable.split('').reverse().join('').replace(/^[\.]/,'');
                                                                    total       = total.toString().split('').reverse().join('').replace(/(?=\d*\,?)(\d{3})/g,'$1.');
                                                                    total       = total.split('').reverse().join('').replace(/^[\.]/,'');
                                                                    aguinaldo   = aguinaldo.toString().split('').reverse().join('').replace(/(?=\d*\,?)(\d{3})/g,'$1.');
                                                                    aguinaldo   = aguinaldo.split('').reverse().join('').replace(/^[\.]/,'');
                                                                    aporte      = aporte.toString().split('').reverse().join('').replace(/(?=\d*\,?)(\d{3})/g,'$1.');
                                                                    aporte      = aporte.split('').reverse().join('').replace(/^[\.]/,'');

                                                                    $(".tablaSalario").append('<tr class="tr" style="background-color:#f5f5f5;"><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Promedios</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;"><strong>'+variable+'</strong></td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;"><strong>'+fijo+'</strong></td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;"><strong>'+total+'</strong></td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;"><strong>'+aguinaldo+'</strong></td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;"><strong>'+aporte+'</strong></td></tr>');
                                                                } else {
                                                                    $(".tablaSalario").empty();
                                                                }


                                                                if(result.eventos != null) {
                                                                    $(".tablaEventos").empty();
                                                                    $(".tablaEventos").html('<thead style="background-color:#f5f5f5;"><tr><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Licencias Justificadas/Injustificadas</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Códigos</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0; display:none;">Fecha</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Cantidad Total</td></tr></thead>');
                                                                    
                                                                    var auxEvenCod  = '';
                                                                    var auxEvenFal  = '';
                                                                    var auxEvenCol  = '';
                                                                    var auxEvenCan  = 0;
                                                                    var auxEvenBan  = true;
                                                                    var html        = '';

                                                                    for (var i = 0; i < result.eventos.length; i++) {
                                                                        if (result.eventos[i].color == "ROJO") {
                                                                            result.eventos[i].color = "red";
                                                                        } else if(result.eventos[i].color == "VERDE") {
                                                                            result.eventos[i].color = "green";
                                                                        } else if(result.eventos[i].color == "AZUL"){
                                                                            result.eventos[i].color = "blue";
                                                                        }

                                                                        if (auxEvenBan === true ){
                                                                            auxEvenCod  = result.eventos[i].codigo;
                                                                            auxEvenFal  = result.eventos[i].falta;
                                                                            auxEvenCol  = result.eventos[i].color;
                                                                            auxEvenCan  = 0;
                                                                            auxEvenBan  = false;
                                                                        }
                        
                                                                        if (i != (result.eventos.length - 1)) {
                                                                            if (result.eventos[i].codigo != auxEvenCod){
                                                                                var html    = '<tr><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+auxEvenFal+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke; color:'+auxEvenCol+'">'+auxEvenCod+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke; display:none;">'+result.eventos[i].mes+"-"+result.eventos[i].ano+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+auxEvenCan+'</td></tr>';
                                                                                $(".tablaEventos").append(html);
                                                                                auxEvenCod  = result.eventos[i].codigo;
                                                                                auxEvenFal  = result.eventos[i].falta;
                                                                                auxEvenCol  = result.eventos[i].color;
                                                                                auxEvenCan  = Number(result.eventos[i].total);
                                                                            } else {
                                                                                auxEvenCan  = auxEvenCan + Number(result.eventos[i].total);
                                                                            }
                                                                        } else {
                                                                            var html    = '';
                        
                                                                            if (result.eventos[i].codigo != auxEvenCod){
                                                                                html        = '<tr><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+auxEvenFal+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke; color:'+auxEvenCol+'">'+auxEvenCod+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke; display:none;">'+result.eventos[i].mes+"-"+result.eventos[i].ano+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+auxEvenCan+'</td></tr>';
                                                                                auxEvenCod  = result.eventos[i].codigo;
                                                                                auxEvenFal  = result.eventos[i].falta;
                                                                                auxEvenCol  = result.eventos[i].color;
                                                                                auxEvenCan  = Number(result.eventos[i].total);
                                                                            }
                        
                                                                            html        = html + '<tr><td class="td" style="text-align:center;">'+auxEvenFal+'</td><td class="td" style="text-align:center; color:'+auxEvenCol+'">'+auxEvenCod+'</td><td class="td" style="text-align:center; display:none;">'+result.eventos[i].mes+"-"+result.eventos[i].ano+'</td><td class="td" style="text-align:center;">'+auxEvenCan+'</td></tr>';
                        
                                                                            $(".tablaEventos").append(html);
                                                                        }
                                                                    }
                                                                } else {
                                                                    $(".tablaEventos").empty();
                                                                }

                                                                if(result.documentos != null) {
                                                                    $(".tablaDocumentos").empty();
                                                                    $(".tablaDocumentos").html('<thead style="background-color:#f5f5f5;"><tr><td class="td"></td></tr></thead>');
    
                                                                    for (var i = 0; i < result.documentos.length; i++) {
                                                                        var tipoDoc     = result.documentos[i].FUNC_TIPO;
                                                                        var nombreDoc   = result.documentos[i].FUNC_DOCUMENTO;
                                                                        var pathDoc     = result.documentos[i].FUNC_PATH;
    
                                                                        var html        = '<tr class="tr"><td class="td" style="text-align:left;"><a href="http://intranet.carsa.com.py/wp-content/themes/sydney/organigrama/img/documentos/192.168.16.116:8080/'+pathDoc+'" target="_blank"> '+nombreDoc+' </a></td></tr>';
                                                                        $(".tablaDocumentos").append(html);
                                                                    }
                                                                } else {
                                                                    $(".tablaDocumentos").empty();
                                                                }
																
																if(result.ends != null) {
                                                                $(".tablaEnds").empty();
                                                                $(".tablaEnds").html('<thead style="background-color:#f5f5f5;"><tr><td class="td"></td></tr></thead>');

                                                                for (var i = 0; i < result.ends.length; i++) {
                                                                    var fechaEnd     = result.ends[i].FECHA + " - ";
                                                                    var eventoEnd   = result.ends[i].EVENTO;
                                                                    var pathEnd     = result.ends[i].ARCHIVO;
																	var nroEnd     = result.ends[i].NRO_EVENTO;
																	
																	var fechaEnd = fechaEnd.replace(" 00:00:00.000"," ");
																	var fechaEnd = fechaEnd.replace("000","");
																	
																	
																	
																	
                                                                    if (i != (result.ends.length - 1)) {
                                                                        var html = '<tr data-nro="'+nroEnd+'" class="tr"><td class="td" style="text-align:left; border-bottom-color:whitesmoke;"><a href="http://intranet.carsa.com.py/wp-content/themes/sydney/organigramatesting/img/ends/192.168.16.116:8082/'+pathEnd+'" target="_blank"> '+fechaEnd+eventoEnd+' </a></td></tr>';
                                                                    } else {
                                                                        var html = '<tr data-nro="'+nroEnd+'" class="tr"><td class="td" style="text-align:left;"><a href="http://intranet.carsa.com.py/wp-content/themes/sydney/organigramatesting/img/ends/192.168.16.116:8082/'+pathEnd+'" target="_blank"> '+fechaEnd+eventoEnd+' </a></td></tr>';
                                                                    }

                                                                    $(".tablaEnds").append(html);
																	
																	
                                                                }
																var seen = {};
																	$('.tablaEnds tr').each(function() {
																	  var txt = $(this).text();
																	  var numero = $(this).data("nro");
																	  if (seen[txt]){
																		$(this).css("display","none"); 
																		$(this).addClass("repetido_"+numero);
																	  }else{
																		seen[txt] = true;
																		$(this).addClass("primero_"+numero);
																		$(this).addClass("only");
																	  }
																	});
                                                            } else {
                                                                $(".tablaEnds").empty();
                                                            }
																
																

                                                                if(result.capacitaciones != null) {
                                                                    $(".tablaCapacitaciones").empty();
                                                                    $(".tablaCapacitaciones").html('<thead style="background-color:#f5f5f5;"><tr><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Número</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Empresa</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Curso</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Año</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Mes</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Horas</td></tr></thead>');
                                                                    
                                                                    for (var i = 0; i < result.capacitaciones.length; i++) {
                                                                        var nroCapacitacion = result.capacitaciones[i].FUNC_NRO_CAPACITACION;
                                                                        var empCapacitacion = result.capacitaciones[i].FUNC_EMPRESA;
                                                                        var curCapacitacion = result.capacitaciones[i].FUNC_CURSO;
                                                                        var anoCapacitacion = result.capacitaciones[i].FUNC_ANHO;
                                                                        var mesCapacitacion = result.capacitaciones[i].FUNC_MES;
                                                                        var horCapacitacion = result.capacitaciones[i].FUNC_CANT_HORA;
    
                                                                        if (i != (result.capacitaciones.length - 1)) {
                                                                            var html = '<tr class="tr"><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+nroCapacitacion+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+empCapacitacion+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+curCapacitacion+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+anoCapacitacion+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+mesCapacitacion+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+horCapacitacion+'</td></tr>';
                                                                        } else {
                                                                            var html = '<tr class="tr"><td class="td" style="text-align:center;">'+nroCapacitacion+'</td><td class="td" style="text-align:center;">'+empCapacitacion+'</td><td class="td" style="text-align:center;">'+curCapacitacion+'</td><td class="td" style="text-align:center;">'+anoCapacitacion+'</td><td class="td" style="text-align:center;">'+mesCapacitacion+'</td><td class="td" style="text-align:center;">'+horCapacitacion+'</td></tr>';
                                                                        }
    
                                                                        $(".tablaCapacitaciones").append(html);
                                                                    }
                                                                } else {
                                                                    $(".tablaCapacitaciones").empty();
                                                                }
    
                                                                if(result.anotaciones != null) {
                                                                    $(".tablaAnotaciones").empty();
                                                                    $(".tablaAnotaciones").html('<thead style="background-color:#f5f5f5;"><tr><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Número</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Fecha</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Evento</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Observación</td></tr></thead>');
                                                                    
                                                                    for (var i = 0; i < result.anotaciones.length; i++) {
                                                                        var nroAnotacion = result.anotaciones[i].FUNC_NRO_ANOTACION;
                                                                        var fecAnotacion = result.anotaciones[i].FUNC_FECHA;
                                                                        var eveAnotacion = result.anotaciones[i].FUNC_EVENTO;
                                                                        var obsAnotacion = result.anotaciones[i].FUNC_OBSERVACION;
    
                                                                        if (i != (result.anotaciones.length - 1)) {
                                                                            var html = '<tr class="tr"><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+nroAnotacion+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+fecAnotacion+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+eveAnotacion+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+obsAnotacion+'</td></tr>';
                                                                        } else {
                                                                            var html = '<tr class="tr"><td class="td" style="text-align:center;">'+nroAnotacion+'</td><td class="td" style="text-align:center;">'+fecAnotacion+'</td><td class="td" style="text-align:center;">'+eveAnotacion+'</td><td class="td" style="text-align:center;">'+obsAnotacion+'</td></tr>';
                                                                        }
    
                                                                        $(".tablaAnotaciones").append(html);
                                                                    }
                                                                } else {
                                                                    $(".tablaAnotaciones").empty();
                                                                }

                                                                if(result.antlaborales != null) {
																$(".tablaAntLaborales").empty();
																// $(".tablaAntLaborales").html('<thead style="background-color:#f5f5f5;"><tr><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Número</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Empresa</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Fecha Desde</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Fecha Hasta</td></tr></thead>');
																
																for (var i = 0; i < result.antlaborales.length; i++) {
																	var nroAntLaboral = result.antlaborales[i].FUNC_NRO_ANTECEDENTE;
																	var empAntLaboral = result.antlaborales[i].FUNC_EMPRESA;
																	var desAntLaboral = result.antlaborales[i].FUNC_FECHA_DESDE;
																	var hasAntLaboral = result.antlaborales[i].FUNC_FECHA_HASTA;
																	var empAntLaboral = sentenceCase(empAntLaboral);
																	if (i != (result.antlaborales.length - 1)) {
																		var html = '<span>'+nroAntLaboral+'  <b>'+empAntLaboral+'</b>. Desde:'+desAntLaboral+'; Hasta:'+hasAntLaboral+'.</span><br>';
																	} else {
																		 var html = '<span>'+nroAntLaboral+'  <b>'+empAntLaboral+'</b>. Desde:'+desAntLaboral+'; Hasta:'+hasAntLaboral+'.</span><br>';
																	}
																	$("#titinfolaboral").fadeIn();
																	$(".tablaAntLaborales").append(html).fadeIn();
																}
															} else {
																$(".tablaAntLaborales").hide().empty();
																$("#titinfolaboral").hide();
															}

                                                                if (result.movimientos != null ) {
                                                                    var auxCargo = "";

                                                                    $(".tablaMovimientos").empty();
                                                                    $(".tablaMovimientos").append('<thead style="background-color:#f5f5f5;"><tr class="tr"><td class="td" style="text-align:center;"> Desde </td><td class="td" style="text-align:center;">  Cargo </td><td class="td" style="text-align:center;">  Departamento/Oficina  </td></tr></thead>');

                                                                    for (var i = 0; i < result.movimientos.length; i++) {
                                                                        if(result.movimientos[i].departamento != auxCargo) {
                                                                            var html='<tr class="tr"><td class="td" style="text-align:center; text-transform: capitalize;">'+result.movimientos[i].desde+'</td><td class="td" style="text-align:center; text-transform: capitalize;">'+jsUcfirst(result.movimientos[i].cargo)+'</td><td class="td" style="text-align:center; text-transform: capitalize;">'+jsUcfirst(result.movimientos[i].departamento)+'</td></tr>';
                                                                            $(".tablaMovimientos").append(html);
                                                                            auxCargo = result.movimientos[i].departamento;
                                                                        }    
                                                                    }
                                                                } else {
                                                                    $(".tablaMovimientos").empty();
                                                                }
                                                            },
                                                            dataType:"json",
                                                            type:"POST"
                                                        });




                                                    var codFunc         = $(this).children().children(".infoContent").data("codFunc");
                                                    var nombre          = $(this).children().children(".infoContent").data("name");
                                                    var img             = $(this).children().children(".infoContent").data("img");
                                                    var cargo           = $(this).children().children(".infoContent").data("cargo");
                                                    var jsonInfo        = $(this).children().children(".infoContent").data("json");
                                                    var str             = nombre;
                                                    var nombre          = str.split("-");
                                                    var antiguedad      = $(this).children().children(".infoContent").data("anti");
                                                    var fullname        = $(this).children().children(".infoContent").data("fullname");

                                                    $("#nombre").html(toTitleCase(fullname));
                                                    $("#infoCodigo").html(codFunc);
                                                    $("#infoCargo").html(cargo);
                                                    $("#infoantiguedad").html(antiguedad);
                                                    $(".imgProfile").attr("style","height: 63%;background-image: url("+img+");background-color: #cccccc;background-repeat:no-repeat;background-size: 100% 115%;height: 201px;width:260px;border: 3px solid #c59d4c;border-radius: 17px;");
                                                    $(".divInfo").hide();
                                                    $("#"+jsonInfo).show();
                                                });
                                            }

                                            ultnivel = result[i].nivel;
                                            ultid = result[i].id;
                                            ultsuper = result[i].superior;
                                        }
                                    }

                                },
                                dataType:"json",
                                type:"POST"

                            });




                        $('.node2').on( "click", function() {
                            elements.addClass('active');
                            $("#miniloading").fadeIn();
                            $(".tablaMovimientos").empty();
                            $(".tablaSalario").empty();
                            $(".tablaLogros").empty();
                            $(".tablaEventos").empty();
                            $(".tablaDocumentos").empty();
                            $(".tablaCapacitaciones").empty();
                            $(".tablaAnotaciones").empty();
                            $(".tablaAntLaborales").empty();
                            $(".tablaLogros").append('<thead style="background-color:#f5f5f5;"><tr class="tr"><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;"> Meses </td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;"> Tipo de Producción </td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;"> Meta </td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;"> Logrado </td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;"> Ratio </td></tr></thead>');
                            $("#infoedad").html("");
                            $("#cant_depen").html("");
                            $("#cant_cont").html("");
                            $("#vivienda").html("");
                            $("#movilidad").html("");
                            $("#qbackup").html("");
                            $("#dbackup").html("");
                            $("#infogrado").html("");
                            $("#infoNroDocumento").html("");
                            $("#infoGerencia").html("");
                            $("#infoOrganigrama").html("");
                            $("#infoSuperior").html("");
                            $("#infoFechaIngreso").html("");
                            $("#infoCodigo").html(localStorage.getItem("idPersona"));


                            $.ajax(
                                {
                                    url:"inc/funciones.php?funcion=selectTablas&id="+localStorage.getItem("idPersona"),
                                    success: function(result)
                                    {


                                        $("#infoedad").html("");
                                        $("#cant_depen").html("");
                                        $("#cant_cont").html("");
                                        $("#vivienda").html("");
                                        $("#movilidad").html("");
                                        $("#qbackup").html("");
                                        $("#dbackup").html("");


                                        $("#miniloading").fadeOut();


                                        $(".tabladependencia").empty();
                                        $(".tablahobbies").empty();
                                        $(".tabladependencia").append('<tr class="tr"><td class="td" style="color:black">Familiares Directos</td><td class="td" style="color:black">Nombre y Apellido</td> </tr>')
                                        $(".tablahobbies").append('<tr class="tr"><td class="td" style="color:black">Hobbie</td><td class="td" style="color:black">Observaci&#243;n</td> </tr>')


                                        $("#infogrado").html("");
                                        $("#infoNroDocumento").html("");
                                        $("#infoGerencia").html("");
                                        $("#infoOrganigrama").html("");
                                        $("#infoSuperior").html("");
                                        $("#infoFechaIngreso").html("");
                                        $("#infouni").html("");
                                        $("#infomasterado").html("");
                                        $("#infodiplomado").html("");





                                        if(result.informacion != null)
                                        {
                                            $("#infogrado").html(result.informacion[0].GRADO_ACADEMICO);
                                            $("#infouni").html(result.informacion[0].UNIVERSIDAD);
                                            $("#infomasterado").html(result.informacion[0].MASTERADO);
                                            $("#infodiplomado").html(result.informacion[0].DIPLOMADO);
                                            $("#infoedad").html(result.informacion[0].EDAD);
                                            $("#cant_depen").html(result.informacion[0].CAN_PER_DEP_ECO);
                                            $("#cant_cont").html(result.informacion[0].CAN_CONT_GASTOS);
                                            $("#vivienda").html(result.informacion[0].TIPO_VIVIENDA);
                                            $("#movilidad").html(result.informacion[0].MOV_PROPIA);
                                            $("#infoNroDocumento").html(result.informacion[0].NRO_CEDULA);
                                            $("#infoGerencia").html(jsUcfirst(result.informacion[0].GERENCIA));
                                            $("#infoFechaIngreso").html(result.informacion[0].FECHA_INGRESO);

                                            var nomGer = "'"+result.informacion[0].GERENCIA+"'";
                                            var verOrganigrama = '<button type="button" onclick="getOrganigrama(' + result.informacion[0].COD_GERENCIA + ', '+ nomGer +');">Ver</button>';
                                            $("#infoOrganigrama").html(verOrganigrama);

                                            if (result.informacion[0].SUPERIOR_INMEDIATO != ''){
                                                document.getElementById("titSuperior").style.display = "";
                                                document.getElementById("saltSuperior").style.display = "";
                                                document.getElementById("infoSuperior").style.display = "";
                                                $("#titSuperior").html('Superior: ');
                                                $("#infoSuperior").html(jsUcfirst(result.informacion[0].SUPERIOR_INMEDIATO));
                                            } else {
                                                document.getElementById("titSuperior").style.display = "none";
                                                document.getElementById("saltSuperior").style.display = "none";
                                                document.getElementById("infoSuperior").style.display = "none";
                                                $("#titSuperior").html('');
                                                $("#infoSuperior").html('');
                                            }
                                        }

                                        if(result.dependencia != null)
                                        {
                                            $(".tabladependencia").fadeIn();
                                            $("#datosdependencia").fadeIn();
                                            for (var i = 0; i < result.dependencia.length; i++)
                                            {


                                                var relacion = result.dependencia[i].PARENTESCO;
                                                var nombre = result.dependencia[i].NOMBRE_COMPLETO_DEP;
                                                var html='<tr class="tr"><td class="td">'+relacion+'</td><td class="td">'+nombre+'</td></tr>';
                                                $(".tabladependencia").append(html);
                                                //alert(nombre);


                                            }

                                        }else {
                                            $(".tabladependencia").fadeOut()
                                            $("#datosdependencia").fadeOut()
                                        }

                                        if(result.academico != null) {
                                            document.getElementById("titinfogrado").style.display = "";

                                            for (var i = 0; i < result.academico.length; i++) {
                                                var cursado = result.academico[i].ANTECEDENTE_ACADEMICO;
                                                cursado     = cursado.replace("/", " / ");
                                                cursado     = cursado.replace("/culminado", " / culminado");
                                                cursado     = cursado.replace("/en", " / en");
                                                cursado     = cursado.replace("/proceso", " / proceso");
                                                var html    = '<spam style="text-transform:capitalize;">'+cursado+'</spam><br>';
                                                $("#infogrado").append(html);
                                            }
                                        } else {
                                            document.getElementById("titinfogrado").style.display = "none";
                                        }


                                        if(result.hobbies != null){
                                            for (var i = 0; i < result.hobbies.length; i++) {
                                                var hobbie = result.hobbies[i].HOBBIE;
                                                var OBSERVACION = result.hobbies[i].OBSERVACION;
                                                var html='<tr class="tr"><td class="td">'+hobbie+'</td><td class="td">'+OBSERVACION+'</td></tr>';
                                                
                                                $(".tablahobbies").append(html);
                                                $(".tablahobbies").show();
                                            }
                                        } else {
                                            $(".tablahobbies").hide();
                                        }

                                        if(result.backups != null ) {
                                            document.getElementById("titqbackup").style.display = "none";
                                            document.getElementById("titdbackup").style.display = "none";

                                            for (var i = 0; i < result.backups.length; i++) {
                                                if(result.backups[i].TIPO == "1") {
                                                    document.getElementById("titqbackup").style.display = "";
                                                    $("#qbackup").append('<spam>-'+ toTitleCase(result.backups[i].NOMBRE_BACKUP)+'</spam><br>');
                                                } else {
                                                    document.getElementById("titdbackup").style.display = "";
                                                    $("#dbackup").append('<spam>-'+toTitleCase(result.backups[i].NOMBRE_BACKUP)+'</spam><br>');
                                                }
                                            }
                                        } else {
                                            document.getElementById("titqbackup").style.display = "none";
                                            document.getElementById("titdbackup").style.display = "none";
                                        }

                                        var promedioLogrado     = 0;
                                        if ( result.logros != null) {
                                            for (var i = 0; i < result.logros.length; i++) {
                                                var meta                = result.logros[i].META.split(".");
                                                var logro               = result.logros[i].LOGRADO.split(".");

                                                meta[0]                 = meta[0].toString().split('').reverse().join('').replace(/(?=\d*\,?)(\d{3})/g,'$1.');
                                                meta[0]                 = meta[0].split('').reverse().join('').replace(/^[\.]/,'');
                                                logro[0]                = logro[0].toString().split('').reverse().join('').replace(/(?=\d*\,?)(\d{3})/g,'$1.');
                                                logro[0]                = logro[0].split('').reverse().join('').replace(/^[\.]/,'');

                                                var color               = "";
                                                var texto               = "";

                                                result.logros[i].COLOR  = result.logros[i].COLOR.replace(/ /g , "");

                                                if (result.logros[i].COLOR == "ROJO") {
                                                    color   = "red";
                                                    texto   = "white";
                                                } else if (result.logros[i].COLOR == "VERDE") {
                                                    color   = "green";
                                                    texto   = "white";
                                                } else {
                                                    color   = "#eae70a";
                                                    texto   = "black";
                                                }

                                                if (i != (result.logros.length - 1)) {
                                                    var html = '<tr class="tr"><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+result.logros[i].PERIODO+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+result.logros[i].TIPO+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+meta[0]+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+logro[0]+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke; background-color:'+result.logros[i].COLOR+'; color:'+result.logros[i].COLOR_TEXTO+'">'+result.logros[i].RATIO+'%</td></tr>';
                                                } else {
                                                    var html = '<tr class="tr"><td class="td" style="text-align:center;">'+result.logros[i].PERIODO+'</td><td class="td" style="text-align:center;">'+result.logros[i].TIPO+'</td><td class="td" style="text-align:center;">'+meta[0]+'</td><td class="td" style="text-align:center;">'+logro[0]+'</td><td class="td" style="text-align:center; background-color:'+result.logros[i].COLOR+'; color:'+result.logros[i].COLOR_TEXTO+'">'+result.logros[i].RATIO+'%</td></tr>';
                                                }
                                                
                                                $(".tablaLogros").append(html);

                                                promedioLogrado = promedioLogrado + parseInt(result.logros[i].RATIO);
                                            }
                                            promedioLogrado = (promedioLogrado / result.logros.length);

                                            var htmlPromedio= '<tr class="tr" style="background-color:#f5f5f5;"><td class="td" colspan="3"></td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;"><strong>Promedio</strong></td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;"><strong>'+promedioLogrado.toFixed(2)+'%</strong></td></tr>';
                                            
                                            $(".tablaLogros").append(htmlPromedio);
                                        } else {
                                            $(".tablaLogros").empty();
                                        }

                                        var fijo        = 0;
                                        var variable    = 0;
                                        var total       = 0
                                        var aguinaldo   = 0;
                                        var aporte      = 0;

                                        if (result.salario != null) {
                                            $(".tablaSalario").empty();
                                            $(".tablaSalario").append('<thead style="background-color:#f5f5f5;"><tr class="tr"><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Meses</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Salario Variable(SV)</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Salario Fijo(SF)</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Salario Total(ST)</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Aguinaldo</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">IPS</td></tr></thead>');

                                            for (var i = 0; i < result.salario.length; i++) {
                                                fijo            = fijo + parseInt(result.salario[i].fijo);
                                                variable        = variable + parseInt(result.salario[i].variable);
                                                total           = total + parseInt(result.salario[i].total);
                                                aguinaldo       = aguinaldo + parseInt(result.salario[i].aguinaldo);
                                                aporte          = aporte + parseInt(result.salario[i].aporte);

                                                var VARIABLE    = result.salario[i].variable;
                                                var FIJO        = result.salario[i].fijo;
                                                var TOTAL       = result.salario[i].total;
                                                var AGUINALDO   = result.salario[i].aguinaldo;
                                                var APORTE      = result.salario[i].aporte;

                                                AGUINALDO       = AGUINALDO.toString().split('').reverse().join('').replace(/(?=\d*\,?)(\d{3})/g,'$1.');
                                                AGUINALDO       = AGUINALDO.split('').reverse().join('').replace(/^[\.]/,'');
                                                APORTE          = APORTE.toString().split('').reverse().join('').replace(/(?=\d*\,?)(\d{3})/g,'$1.');
                                                APORTE          = APORTE.split('').reverse().join('').replace(/^[\.]/,'');
                                                VARIABLE        = VARIABLE.toString().split('').reverse().join('').replace(/(?=\d*\,?)(\d{3})/g,'$1.');
                                                VARIABLE        = VARIABLE.split('').reverse().join('').replace(/^[\.]/,'');
                                                FIJO            = FIJO.toString().split('').reverse().join('').replace(/(?=\d*\,?)(\d{3})/g,'$1.');
                                                FIJO            = FIJO.split('').reverse().join('').replace(/^[\.]/,'');
                                                TOTAL           = TOTAL.toString().split('').reverse().join('').replace(/(?=\d*\,?)(\d{3})/g,'$1.');
                                                TOTAL           = TOTAL.split('').reverse().join('').replace(/^[\.]/,'');

                                                if (i != (result.salario.length - 1)) {
                                                    var html = '<tr class="tr"> <td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+result.salario[i].periodo+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+VARIABLE+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+FIJO+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+TOTAL+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+AGUINALDO+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+APORTE+'</td></tr>';
                                                } else {
                                                    var html = '<tr class="tr"> <td class="td" style="text-align:center;">'+result.salario[i].periodo+'</td><td class="td" style="text-align:center;">'+VARIABLE+'</td><td class="td" style="text-align:center;">'+FIJO+'</td><td class="td" style="text-align:center;">'+TOTAL+'</td><td class="td" style="text-align:center;">'+AGUINALDO+'</td><td class="td" style="text-align:center;">'+APORTE+'</td></tr>';
                                                }

                                                $(".tablaSalario").append(html);
                                            }

                                            total       = (total/result.salario.length);
                                            variable    = (variable/result.salario.length);
                                            fijo        = (fijo/result.salario.length);
                                            fijo        = fijo.toFixed(0);
                                            variable    = variable.toFixed(0);
                                            total       = total.toFixed(0);
                                            fijo        = fijo.toString().split('').reverse().join('').replace(/(?=\d*\,?)(\d{3})/g,'$1.');
                                            fijo        = fijo.split('').reverse().join('').replace(/^[\.]/,'');
                                            variable    = variable.toString().split('').reverse().join('').replace(/(?=\d*\,?)(\d{3})/g,'$1.');
                                            variable    = variable.split('').reverse().join('').replace(/^[\.]/,'');
                                            total       = total.toString().split('').reverse().join('').replace(/(?=\d*\,?)(\d{3})/g,'$1.');
                                            total       = total.split('').reverse().join('').replace(/^[\.]/,'');
                                            aguinaldo   = aguinaldo.toString().split('').reverse().join('').replace(/(?=\d*\,?)(\d{3})/g,'$1.');
                                            aguinaldo   = aguinaldo.split('').reverse().join('').replace(/^[\.]/,'');
                                            aporte      = aporte.toString().split('').reverse().join('').replace(/(?=\d*\,?)(\d{3})/g,'$1.');
                                            aporte      = aporte.split('').reverse().join('').replace(/^[\.]/,'');
                                            
                                            $(".tablaSalario").append('<tr class="tr" style="background-color:#f5f5f5;"><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Promedios</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;"><strong>'+variable+'</strong></td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;"><strong>'+fijo+'</strong></td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;"><strong>'+total+'</strong></td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;"><strong>'+aguinaldo+'</strong></td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;"><strong>'+aporte+'</strong></td></tr>');
                                        } else {
                                            $(".tablaSalario").empty();
                                        }


                                        if(result.eventos != null) {
                                            $(".tablaEventos").empty();
                                            $(".tablaEventos").html('<thead style="background-color:#f5f5f5;"><tr><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Licencias Justificadas/Injustificadas</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Códigos</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0; display:none;">Fecha</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Cantidad Total</td></tr></thead>');
                                            
                                            var auxEvenCod  = '';
                                            var auxEvenFal  = '';
                                            var auxEvenCol  = '';
                                            var auxEvenCan  = 0;
                                            var auxEvenBan  = true;
                                            var html        = '';
                                            
                                            for (var i = 0; i < result.eventos.length; i++) {
                                                if (result.eventos[i].color == "ROJO") {
                                                    result.eventos[i].color = "red";
                                                } else if(result.eventos[i].color == "VERDE") {
                                                    result.eventos[i].color = "green";
                                                } else if(result.eventos[i].color == "AZUL") {
                                                    result.eventos[i].color = "blue";
                                                }

                                                if (auxEvenBan === true ){
                                                    auxEvenCod  = result.eventos[i].codigo;
                                                    auxEvenFal  = result.eventos[i].falta;
                                                    auxEvenCol  = result.eventos[i].color;
                                                    auxEvenCan  = 0;
                                                    auxEvenBan  = false;
                                                }

                                                if (i != (result.eventos.length - 1)) {
                                                    if (result.eventos[i].codigo != auxEvenCod){
                                                        var html    = '<tr><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+auxEvenFal+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke; color:'+auxEvenCol+'">'+auxEvenCod+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke; display:none;">'+result.eventos[i].mes+"-"+result.eventos[i].ano+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+auxEvenCan+'</td></tr>';
                                                        $(".tablaEventos").append(html);
                                                        auxEvenCod  = result.eventos[i].codigo;
                                                        auxEvenFal  = result.eventos[i].falta;
                                                        auxEvenCol  = result.eventos[i].color;
                                                        auxEvenCan  = Number(result.eventos[i].total);
                                                    } else {
                                                        auxEvenCan  = auxEvenCan + Number(result.eventos[i].total);
                                                    }
                                                } else {
                                                    var html    = '';

                                                    if (result.eventos[i].codigo != auxEvenCod){
                                                        html        = '<tr><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+auxEvenFal+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke; color:'+auxEvenCol+'">'+auxEvenCod+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke; display:none;">'+result.eventos[i].mes+"-"+result.eventos[i].ano+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+auxEvenCan+'</td></tr>';
                                                        auxEvenCod  = result.eventos[i].codigo;
                                                        auxEvenFal  = result.eventos[i].falta;
                                                        auxEvenCol  = result.eventos[i].color;
                                                        auxEvenCan  = Number(result.eventos[i].total);
                                                    }

                                                    html        = html + '<tr><td class="td" style="text-align:center;">'+auxEvenFal+'</td><td class="td" style="text-align:center; color:'+auxEvenCol+'">'+auxEvenCod+'</td><td class="td" style="text-align:center; display:none;">'+result.eventos[i].mes+"-"+result.eventos[i].ano+'</td><td class="td" style="text-align:center;">'+auxEvenCan+'</td></tr>';

                                                    $(".tablaEventos").append(html);
                                                }
                                            }
                                        } else {
                                            $(".tablaEventos").empty();
                                        }

                                        if(result.documentos != null) {
                                            $(".tablaDocumentos").empty();
                                            $(".tablaDocumentos").html('<thead style="background-color:#f5f5f5;"><tr><td class="td"></td></tr></thead>');

                                            for (var i = 0; i < result.documentos.length; i++) {
                                                var tipoDoc     = result.documentos[i].FUNC_TIPO;
                                                var nombreDoc   = result.documentos[i].FUNC_DOCUMENTO;
                                                var pathDoc     = result.documentos[i].FUNC_PATH;

                                                var html        = '<tr class="tr"><td class="td" style="text-align:left;"><a href="http://intranet.carsa.com.py/wp-content/themes/sydney/organigrama/img/documentos/192.168.16.116:8080/'+pathDoc+'" target="_blank"> '+nombreDoc+' </a></td></tr>';
                                                $(".tablaDocumentos").append(html);
                                            }
                                        } else {
                                            $(".tablaDocumentos").empty();
                                        }
															
															
															if(result.ends != null) {
                                                                $(".tablaEnds").empty();
                                                                $(".tablaEnds").html('<thead style="background-color:#f5f5f5;"><tr><td class="td"></td></tr></thead>');

                                                                for (var i = 0; i < result.ends.length; i++) {
                                                                    var fechaEnd     = result.ends[i].FECHA + " - ";
                                                                    var eventoEnd   = result.ends[i].EVENTO;
                                                                    var pathEnd     = result.ends[i].ARCHIVO;
																	var nroEnd     = result.ends[i].NRO_EVENTO;
																	
																	var fechaEnd = fechaEnd.replace(" 00:00:00.000"," ");
																	var fechaEnd = fechaEnd.replace("000","");
																	
																	
																	
																	
                                                                    if (i != (result.ends.length - 1)) {
                                                                        var html = '<tr data-nro="'+nroEnd+'" class="tr"><td class="td" style="text-align:left; border-bottom-color:whitesmoke;"><a href="http://intranet.carsa.com.py/wp-content/themes/sydney/organigramatesting/img/ends/192.168.16.116:8082/'+pathEnd+'" target="_blank"> '+fechaEnd+eventoEnd+' </a></td></tr>';
                                                                    } else {
                                                                        var html = '<tr data-nro="'+nroEnd+'" class="tr"><td class="td" style="text-align:left;"><a href="http://intranet.carsa.com.py/wp-content/themes/sydney/organigramatesting/img/ends/192.168.16.116:8082/'+pathEnd+'" target="_blank"> '+fechaEnd+eventoEnd+' </a></td></tr>';
                                                                    }

                                                                    $(".tablaEnds").append(html);
																	
																	
                                                                }
																var seen = {};
																	$('.tablaEnds tr').each(function() {
																	  var txt = $(this).text();
																	  var numero = $(this).data("nro");
																	  if (seen[txt]){
																		$(this).css("display","none"); 
																		$(this).addClass("repetido_"+numero);
																	  }else{
																		seen[txt] = true;
																		$(this).addClass("primero_"+numero);
																		$(this).addClass("only");
																	  }
																	});
                                                            } else {
                                                                $(".tablaEnds").empty();
                                                            }

                                        if(result.capacitaciones != null) {
                                            $(".tablaCapacitaciones").empty();
                                            $(".tablaCapacitaciones").html('<thead style="background-color:#f5f5f5;"><tr><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Número</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Empresa</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Curso</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Año</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Mes</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Horas</td></tr></thead>');
                                            
                                            for (var i = 0; i < result.capacitaciones.length; i++) {
                                                var nroCapacitacion = result.capacitaciones[i].FUNC_NRO_CAPACITACION;
                                                var empCapacitacion = result.capacitaciones[i].FUNC_EMPRESA;
                                                var curCapacitacion = result.capacitaciones[i].FUNC_CURSO;
                                                var anoCapacitacion = result.capacitaciones[i].FUNC_ANHO;
                                                var mesCapacitacion = result.capacitaciones[i].FUNC_MES;
                                                var horCapacitacion = result.capacitaciones[i].FUNC_CANT_HORA;

                                                if (i != (result.capacitaciones.length - 1)) {
                                                    var html = '<tr class="tr"><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+nroCapacitacion+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+empCapacitacion+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+curCapacitacion+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+anoCapacitacion+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+mesCapacitacion+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+horCapacitacion+'</td></tr>';
                                                } else {
                                                    var html = '<tr class="tr"><td class="td" style="text-align:center;">'+nroCapacitacion+'</td><td class="td" style="text-align:center;">'+empCapacitacion+'</td><td class="td" style="text-align:center;">'+curCapacitacion+'</td><td class="td" style="text-align:center;">'+anoCapacitacion+'</td><td class="td" style="text-align:center;">'+mesCapacitacion+'</td><td class="td" style="text-align:center;">'+horCapacitacion+'</td></tr>';
                                                }

                                                $(".tablaCapacitaciones").append(html);
                                            }
                                        } else {
                                            $(".tablaCapacitaciones").empty();
                                        }

                                        if(result.anotaciones != null) {
                                            $(".tablaAnotaciones").empty();
                                            $(".tablaAnotaciones").html('<thead style="background-color:#f5f5f5;"><tr><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Número</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Fecha</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Evento</td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">Observación</td></tr></thead>');
                                            
                                            for (var i = 0; i < result.anotaciones.length; i++) {
                                                var nroAnotacion = result.anotaciones[i].FUNC_NRO_ANOTACION;
                                                var fecAnotacion = result.anotaciones[i].FUNC_FECHA;
                                                var eveAnotacion = result.anotaciones[i].FUNC_EVENTO;
                                                var obsAnotacion = result.anotaciones[i].FUNC_OBSERVACION;

                                                if (i != (result.anotaciones.length - 1)) {
                                                    var html = '<tr class="tr"><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+nroAnotacion+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+fecAnotacion+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+eveAnotacion+'</td><td class="td" style="text-align:center; border-bottom-color:whitesmoke;">'+obsAnotacion+'</td></tr>';
                                                } else {
                                                    var html = '<tr class="tr"><td class="td" style="text-align:center;">'+nroAnotacion+'</td><td class="td" style="text-align:center;">'+fecAnotacion+'</td><td class="td" style="text-align:center;">'+eveAnotacion+'</td><td class="td" style="text-align:center;">'+obsAnotacion+'</td></tr>';
                                                }

                                                $(".tablaAnotaciones").append(html);
                                            }
                                        } else {
                                            $(".tablaAnotaciones").empty();
                                        }

                                        if (result.antlaborales != null) {
											for (var i = 0; i < result.antlaborales.length; i++) {
												var nroAntLaboral = result.antlaborales[i].FUNC_NRO_ANTECEDENTE;
												var empAntLaboral = result.antlaborales[i].FUNC_EMPRESA;
												var desAntLaboral = result.antlaborales[i].FUNC_FECHA_DESDE;
												var hasAntLaboral = result.antlaborales[i].FUNC_FECHA_HASTA;
												var empAntLaboral = sentenceCase(empAntLaboral);
												if (i != (result.antlaborales.length - 1)) {
													var html = '<span>'+nroAntLaboral+'  <b>'+empAntLaboral+'</b>. Desde:'+desAntLaboral+'; Hasta:'+hasAntLaboral+'.</span><br>';
												} else {
													 var html = '<span>'+nroAntLaboral+'  <b>'+empAntLaboral+'</b>. Desde:'+desAntLaboral+'; Hasta:'+hasAntLaboral+'.</span><br>';
												}

												$(".tablaAntLaborales").append(html);
												$(".tablaAntLaborales").show();
												$("#titinfolaboral").fadeIn();
											}
										} else {
											$(".tablaAntLaborales").hide().empty();
											$("#titinfolaboral").hide();
										}

                                        if (result.movimientos != null ) {
                                            var auxCargo = "";

                                            $(".tablaMovimientos").empty();
                                            $(".tablaMovimientos").append('<thead style="background-color:#f5f5f5;"><tr class="tr"><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;"> Desde </td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">  Cargo </td><td class="td" style="text-align:center; font-weight:bold; color:#000000e0;">  Departamento/Oficina  </td></tr></thead>');

                                            for (var i = 0; i < result.movimientos.length; i++){
                                                if(result.movimientos[i].departamento != auxCargo) {
                                                    var html='<tr class="tr"><td class="td" style="text-align:center; text-transform: capitalize;">'+result.movimientos[i].desde+'</td><td class="td" style="text-align:center; text-transform: capitalize;">'+jsUcfirst(result.movimientos[i].cargo)+'</td><td class="td" style="text-align:center; text-transform: capitalize;">'+jsUcfirst(result.movimientos[i].departamento)+'</td></tr>';
                                                    $(".tablaMovimientos").append(html);
                                                    auxCargo = result.movimientos[i].departamento;
                                                }
                                            }
                                        } else {
                                            $(".tablaMovimientos").empty();
                                        }



                                    },
                                    dataType:"json",
                                    type:"POST"

                                });






                            var codFunc         = $(this).children().children(".infoContent").data("codFunc");
                            var nombre          = $(this).children().children(".infoContent").data("name");
                            var img             = $(this).children().children(".infoContent").data("img");
                            var cargo           = $(this).children().children(".infoContent").data("cargo");
                            var jsonInfo        = $(this).children().children(".infoContent").data("json");
                            var str             = nombre;
                            var nombre          = str.split("-");
                            var antiguedad      = $(this).children().children(".infoContent").data("anti");
                            var fullname        = $(this).children().children(".infoContent").data("fullname");

                            $("#nombre").html( toTitleCase(fullname));
                            $("#infoCodigo").html(codFunc);
                            $("#infoCargo").html(cargo);
                            $("#infoantiguedad").html(antiguedad);
                            $(".imgProfile").attr("style","height: 63%;background-image: url("+img+");background-color: #cccccc;background-repeat:no-repeat;background-size: 100% 115%;height: 201px;width:260px;border: 3px solid #c59d4c;border-radius: 17px;");
                            $(".divInfo").hide();
                            $("#"+jsonInfo).show();




                        });


                        $("#chart-container2").hide();
                        $("#chart-container").fadeIn();
                        /*


                        */
                        $(document).ready(function(){
                            $('.node2').hover(function() {
                                localStorage.setItem("idPersona",$(this).data("id"));
                                $(this).children().addClass('transition');

                            }, function() {
                                $(".node2").children().removeClass('transition');
                            });
                        });




                    }, 500);






                })
                .fail(function () {
                    console.log('orgChart: Failed to get ' + rel + ' data');
                })
                .always(function () {
                    that.endLoading($edge);
                });
        },
        //
        HideFirstParentEnd: function (event) {
            var $topEdge = event.data.topEdge;
            var $node = $topEdge.parent();
            if (this.isInAction($node)) {
                this.switchVerticalArrow($topEdge);
                this.switchHorizontalArrow($node);
            }
        },
        //
        topEdgeClickHandler: function (event) {
            event.stopPropagation();
            var that = this;
            var $topEdge = $(event.target);
            var $node = $(event.delegateTarget);
            var parentState = this.getNodeState($node, 'parent');
            if (parentState.exist) {
                var $parent = $node.closest('table').closest('tr').siblings(':first').find('.node');
                if ($parent.is('.sliding')) { return; }
                // hide the ancestor nodes and sibling nodes of the specified node
                if (parentState.visible) {
                    this.hideParent($node);
                    $parent.one('transitionend', { 'topEdge': $topEdge }, this.HideFirstParentEnd.bind(this));
                } else { // show the ancestors and siblings
                    this.showParent($node);
                }
            } else { // load the new parent node of the specified node by ajax request
                // start up loading status
                if (this.startLoading($topEdge)) {
                    var opts = this.options;
                    var url = $.isFunction(opts.ajaxURL.parent) ? opts.ajaxURL.parent(event.data.nodeData) : opts.ajaxURL.parent + $node[0].id;
                    this.loadNodes('parent', url, $topEdge);
                }
            }
        },
        //
        bottomEdgeClickHandler: function (event)
        {





            event.stopPropagation();
            var $bottomEdge = $(event.target);
            var $node = $(event.delegateTarget);
            var childrenState = this.getNodeState($node, 'children');
            if (childrenState.exist) {
                var $children = $node.closest('tr').siblings(':last');
                if ($children.find('.sliding').length) { return; }
                // hide the descendant nodes of the specified node
                if (childrenState.visible)
                {
                    this.hideChildren($node);

                    $(".divTransversal"+$node[0].id).fadeOut();
                }
                else
                { // show the descendants
                    this.showChildren($node);
                    $(".divTransversal"+$node[0].id).css("display","flex");
                }
            } else { // load the new children nodes of the specified node by ajax request
                if (this.startLoading($bottomEdge)) {
                    var opts = this.options;
                    var url = $.isFunction(opts.ajaxURL.children) ? opts.ajaxURL.children(event.data.nodeData) : opts.ajaxURL.children + $node[0].id;
                    this.loadNodes('children', url, $bottomEdge);
                }
            }



        },
        //
        hEdgeClickHandler: function (event) {
            event.stopPropagation();
            var $hEdge = $(event.target);
            var $node = $(event.delegateTarget);
            var opts = this.options;
            var siblingsState = this.getNodeState($node, 'siblings');
            if (siblingsState.exist) {
                var $siblings = $node.closest('table').parent().siblings();
                if ($siblings.find('.sliding').length) { return; }
                if (opts.toggleSiblingsResp) {
                    var $prevSib = $node.closest('table').parent().prev();
                    var $nextSib = $node.closest('table').parent().next();
                    if ($hEdge.is('.leftEdge')) {
                        if ($prevSib.is('.hidden')) {
                            this.showSiblings($node, 'left');
                        } else {
                            this.hideSiblings($node, 'left');
                        }
                    } else {
                        if ($nextSib.is('.hidden')) {
                            this.showSiblings($node, 'right');
                        } else {
                            this.hideSiblings($node, 'right');
                        }
                    }
                } else {
                    if (siblingsState.visible) {
                        this.hideSiblings($node);
                    } else {
                        this.showSiblings($node);
                    }
                }
            } else {
                // load the new sibling nodes of the specified node by ajax request
                if (this.startLoading($hEdge)) {
                    var nodeId = $node[0].id;
                    var url = (this.getNodeState($node, 'parent').exist) ?
                        ($.isFunction(opts.ajaxURL.siblings) ? opts.ajaxURL.siblings(event.data.nodeData) : opts.ajaxURL.siblings + nodeId) :
                        ($.isFunction(opts.ajaxURL.families) ? opts.ajaxURL.families(event.data.nodeData) : opts.ajaxURL.families + nodeId);
                    this.loadNodes('siblings', url, $hEdge);
                }
            }
        },
        //
        expandVNodesEnd: function (event) {
            event.data.vNodes.removeClass('sliding');
        },
        //
        collapseVNodesEnd: function (event) {
            event.data.vNodes.removeClass('sliding').closest('ul').addClass('hidden');
        },
        // event handler for toggle buttons in Hybrid(horizontal + vertical) OrgChart
        toggleVNodes: function (event) {
            var $toggleBtn = $(event.target);
            var $descWrapper = $toggleBtn.parent().next();
            var $descendants = $descWrapper.find('.node');
            var $children = $descWrapper.children().children('.node');
            if ($children.is('.sliding')) { return; }
            $toggleBtn.toggleClass('fa-plus-square fa-minus-square');
            if ($descendants.eq(0).is('.slide-up')) {
                $descWrapper.removeClass('hidden');
                this.repaint($children.get(0));
                $children.addClass('sliding').removeClass('slide-up').eq(0).one('transitionend', { 'vNodes': $children }, this.expandVNodesEnd);
            } else {
                $descendants.addClass('sliding slide-up').eq(0).one('transitionend', { 'vNodes': $descendants }, this.collapseVNodesEnd);
                $descendants.find('.toggleBtn').removeClass('fa-minus-square').addClass('fa-plus-square');
            }
        },
        //
        createGhostNode: function (event) {
            var $nodeDiv = $(event.target);
            var opts = this.options;
            var origEvent = event.originalEvent;
            var isFirefox = /firefox/.test(window.navigator.userAgent.toLowerCase());
            if (isFirefox) {
                origEvent.dataTransfer.setData('text/html', 'hack for firefox');
            }
            var ghostNode, nodeCover;
            if (!document.querySelector('.ghost-node')) {
                ghostNode = document.createElementNS("http://www.w3.org/2000/svg", "svg");
                ghostNode.classList.add('ghost-node');
                nodeCover = document.createElementNS('http://www.w3.org/2000/svg','rect');
                ghostNode.appendChild(nodeCover);
                $nodeDiv.closest('.orgchart').append(ghostNode);
            } else {
                ghostNode = $nodeDiv.closest('.orgchart').children('.ghost-node').get(0);
                nodeCover = $(ghostNode).children().get(0);
            }
            var transValues = $nodeDiv.closest('.orgchart').css('transform').split(',');
            var scale = Math.abs(window.parseFloat((opts.direction === 't2b' || opts.direction === 'b2t') ? transValues[0].slice(transValues[0].indexOf('(') + 1) : transValues[1]));
            ghostNode.setAttribute('width', $nodeDiv.outerWidth(false));
            ghostNode.setAttribute('height', $nodeDiv.outerHeight(false));
            nodeCover.setAttribute('x',5 * scale);
            nodeCover.setAttribute('y',5 * scale);
            nodeCover.setAttribute('width', 120 * scale);
            nodeCover.setAttribute('height', 40 * scale);
            nodeCover.setAttribute('rx', 4 * scale);
            nodeCover.setAttribute('ry', 4 * scale);
            nodeCover.setAttribute('stroke-width', 1 * scale);
            var xOffset = origEvent.offsetX * scale;
            var yOffset = origEvent.offsetY * scale;
            if (opts.direction === 'l2r') {
                xOffset = origEvent.offsetY * scale;
                yOffset = origEvent.offsetX * scale;
            } else if (opts.direction === 'r2l') {
                xOffset = $nodeDiv.outerWidth(false) - origEvent.offsetY * scale;
                yOffset = origEvent.offsetX * scale;
            } else if (opts.direction === 'b2t') {
                xOffset = $nodeDiv.outerWidth(false) - origEvent.offsetX * scale;
                yOffset = $nodeDiv.outerHeight(false) - origEvent.offsetY * scale;
            }
            if (isFirefox) { // hack for old version of Firefox(< 48.0)
                nodeCover.setAttribute('fill', 'rgb(255, 255, 255)');
                nodeCover.setAttribute('stroke', 'rgb(191, 0, 0)');
                var ghostNodeWrapper = document.createElement('img');
                ghostNodeWrapper.src = 'data:image/svg+xml;utf8,' + (new XMLSerializer()).serializeToString(ghostNode);
                origEvent.dataTransfer.setDragImage(ghostNodeWrapper, xOffset, yOffset);
            } else {
                origEvent.dataTransfer.setDragImage(ghostNode, xOffset, yOffset);
            }
        },
        //
        filterAllowedDropNodes: function ($dragged) {
            var opts = this.options;
            var $dragZone = $dragged.closest('.nodes').siblings().eq(0).find('.node:first');
            var $dragHier = $dragged.closest('table').find('.node');
            this.$chart.data('dragged', $dragged)
                .find('.node').each(function (index, node) {
                if ($dragHier.index(node) === -1) {
                    if (opts.dropCriteria) {
                        if (opts.dropCriteria($dragged, $dragZone, $(node))) {
                            $(node).addClass('allowedDrop');
                        }
                    } else {
                        $(node).addClass('allowedDrop');
                    }
                }
            });
        },
        //
        dragstartHandler: function (event) {
            // if users enable zoom or direction options
            if (this.$chart.css('transform') !== 'none') {
                this.createGhostNode(event);
            }
            this.filterAllowedDropNodes($(event.target));
        },
        //
        dragoverHandler: function (event) {
            event.preventDefault();
            if (!$(event.delegateTarget).is('.allowedDrop')) {
                event.originalEvent.dataTransfer.dropEffect = 'none';
            }
        },
        //
        dragendHandler: function (event) {
            this.$chart.find('.allowedDrop').removeClass('allowedDrop');
        },
        //
        dropHandler: function (event) {
            var $dropZone = $(event.delegateTarget);
            var $dragged = this.$chart.data('dragged');
            var $dragZone = $dragged.closest('.nodes').siblings().eq(0).children();
            var dropEvent = $.Event('nodedrop.orgchart');
            this.$chart.trigger(dropEvent, { 'draggedNode': $dragged, 'dragZone': $dragZone.children(), 'dropZone': $dropZone });
            if (dropEvent.isDefaultPrevented()) {
                return;
            }
            // firstly, deal with the hierarchy of drop zone
            if (!$dropZone.closest('tr').siblings().length) { // if the drop zone is a leaf node
                $dropZone.append('<i class="edge verticalEdge bottomEdge fa"></i>')
                    .parent().attr('colspan', 2)
                    .parent().after('<tr class="lines"><td colspan="2"><div class="downLine"></div></td></tr>'
                    + '<tr class="lines"><td class="rightLine"></td><td class="leftLine"></td></tr>'
                    + '<tr class="nodes"></tr>')
                    .siblings(':last').append($dragged.find('.horizontalEdge').remove().end().closest('table').parent());
            } else {
                var dropColspan = parseInt($dropZone.parent().attr('colspan')) + 2;
                var horizontalEdges = '<i class="edge horizontalEdge rightEdge fa"></i><i class="edge horizontalEdge leftEdge fa"></i>';
                $dropZone.closest('tr').next().addBack().children().attr('colspan', dropColspan);
                if (!$dragged.find('.horizontalEdge').length) {
                    $dragged.append(horizontalEdges);
                }
                $dropZone.closest('tr').siblings().eq(1).children(':last').before('<td class="leftLine topLine"></td><td class="rightLine topLine"></td>')
                    .end().next().append($dragged.closest('table').parent());
                var $dropSibs = $dragged.closest('table').parent().siblings().find('.node:first');
                if ($dropSibs.length === 1) {
                    $dropSibs.append(horizontalEdges);
                }
            }
            // secondly, deal with the hierarchy of dragged node
            var dragColspan = parseInt($dragZone.attr('colspan'));
            if (dragColspan > 2) {
                $dragZone.attr('colspan', dragColspan - 2)
                    .parent().next().children().attr('colspan', dragColspan - 2)
                    .end().next().children().slice(1, 3).remove();
                var $dragSibs = $dragZone.parent().siblings('.nodes').children().find('.node:first');
                if ($dragSibs.length ===1) {
                    $dragSibs.find('.horizontalEdge').remove();
                }
            } else {
                $dragZone.removeAttr('colspan')
                    .find('.bottomEdge').remove()
                    .end().end().siblings().remove();
            }
        },
        //
        touchstartHandler: function (event) {
            console.log("orgChart: touchstart 1: touchHandled=" + this.touchHandled + ", touchMoved=" + this.touchMoved + ", target=" + event.target.innerText);
            if (this.touchHandled)
                return;
            this.touchHandled = true;
            this.touchMoved = false;     // this is so we can work out later if this was a 'press' or a 'drag' touch
            event.preventDefault();
        },
        //
        touchmoveHandler: function (event) {
            if (!this.touchHandled)
                return;
            event.preventDefault();
            if (!this.touchMoved) {
                var nodeIsSelected = $(this).hasClass('focused');
                console.log("orgChart: touchmove 1: " + event.touches.length + " touches, we have not moved, so simulate a drag start", event.touches);
                // TODO: visualise the start of the drag (as would happen on desktop)
                this.simulateMouseEvent(event, 'dragstart');
            }
            this.touchMoved = true;
            var $touching = $(document.elementFromPoint(event.touches[0].clientX, event.touches[0].clientY));
            var $touchingNode = $touching.closest('div.node');

            if ($touchingNode.length > 0) {
                var touchingNodeElement = $touchingNode[0];
                // TODO: simulate the dragover visualisation
                if ($touchingNode.is('.allowedDrop')) {
                    console.log("orgChart: touchmove 2: this node (" + touchingNodeElement.id + ") is allowed to be a drop target");
                    this.touchTargetNode = touchingNodeElement;
                } else {
                    console.log("orgChart: touchmove 3: this node (" + touchingNodeElement.id + ") is NOT allowed to be a drop target");
                    this.touchTargetNode = null;
                }
            } else {
                console.log("orgchart: touchmove 4: not touching a node");
                this.touchTargetNode = null;
            }
        },
        //
        touchendHandler: function (event) {
            console.log("orgChart: touchend 1: touchHandled=" + this.touchHandled + ", touchMoved=" + this.touchMoved + ", " + event.target.innerText + " ");
            if (!this.touchHandled) {
                console.log("orgChart: touchend 2: not handled by us, so aborting");
                return;
            }
            if (this.touchMoved) {
                // we've had movement, so this was a 'drag' touch
                if (this.touchTargetNode) {
                    console.log("orgChart: touchend 3: moved to a node, so simulating drop");
                    var fakeEventForDropHandler = { delegateTarget: this.touchTargetNode };
                    this.dropHandler(fakeEventForDropHandler);
                    this.touchTargetNode = null;
                }
                console.log("orgChart: touchend 4: simulating dragend");
                this.simulateMouseEvent(event, 'dragend');
            }
            else {
                // we did not move, so assume this was a 'press' touch
                console.log("orgChart: touchend 5: moved, so simulating click");
                this.simulateMouseEvent(event, 'click');
            }
            this.touchHandled = false;
        },
        // simulate a mouse event (so we can fake them on a touch device)
        simulateMouseEvent: function (event, simulatedType) {
            // Ignore multi-touch events
            if (event.originalEvent.touches.length > 1) {
                return;
            }
            var touch = event.originalEvent.changedTouches[0];
            var simulatedEvent = document.createEvent('MouseEvents');
            simulatedEvent.initMouseEvent(
                simulatedType,    // type
                true,             // bubbles
                true,             // cancelable
                window,           // view
                1,                // detail
                touch.screenX,    // screenX
                touch.screenY,    // screenY
                touch.clientX,    // clientX
                touch.clientY,    // clientY
                false,            // ctrlKey
                false,            // altKey
                false,            // shiftKey
                false,            // metaKey
                0,                // button
                null              // relatedTarget
            );
            // Dispatch the simulated event to the target element
            event.target.dispatchEvent(simulatedEvent);
        },
        //
        bindDragDrop: function ($node) {
            $node.on('dragstart', this.dragstartHandler.bind(this))
                .on('dragover', this.dragoverHandler.bind(this))
                .on('dragend', this.dragendHandler.bind(this))
                .on('drop', this.dropHandler.bind(this))
                .on('touchstart', this.touchstartHandler.bind(this))
                .on('touchmove', this.touchmoveHandler.bind(this))
                .on('touchend', this.touchendHandler.bind(this));
        },
        // create node
        createNode: function (data) {
            var that = this;
            var opts = this.options;
            var level = data.level;
            if (data.children) {
                $.each(data.children, function (index, child) {
                    child.parentId = data.id;
                });
            }
            // construct the content of node
            var $nodeDiv = $('<div' + (opts.draggable ? ' draggable="true"' : '') + (data[opts.nodeId] ? ' id="' + data[opts.nodeId] + '"' : '') + (data.parentId ? ' data-parent="' + data.parentId + '"' : '') + '>')
                .addClass('node ' + (data.className || '') +  (level > opts.visibleLevel ? ' slide-up' : ''));
            if (opts.nodeTemplate) {
                $nodeDiv.append(opts.nodeTemplate(data));
            } else {
                $nodeDiv.append('<div class="title">' + data[opts.nodeTitle] + '</div>')
                    .append(typeof opts.nodeContent !== 'undefined' ? '<div class="content">' + (data[opts.nodeContent] || '') + '</div>' : '');
            }
            // append 4 direction arrows or expand/collapse buttons
            var flags = data.relationship || '';
            if (opts.verticalLevel && level >= opts.verticalLevel) {
                if ((level + 1) > opts.verticalLevel && Number(flags.substr(2,1))) {
                    var icon = level + 1 > opts.visibleLevel ? 'plus' : 'minus';
                    $nodeDiv.append('<i class="toggleBtn fa fa-' + icon + '-square"></i>');
                }
            } else {
                if (Number(flags.substr(0,1))) {
                    $nodeDiv.append('<i class="edge verticalEdge topEdge fa"></i>');
                }
                if(Number(flags.substr(1,1))) {
                    $nodeDiv.append('<i class="edge horizontalEdge rightEdge fa"></i>' +
                        '<i class="edge horizontalEdge leftEdge fa"></i>');
                }
                if(Number(flags.substr(2,1))) {
                    $nodeDiv.append('<i class="edge verticalEdge bottomEdge fa"></i>')
                        .children('.title').prepend('');
                }
            }

            $nodeDiv.on('mouseenter mouseleave', this.nodeEnterLeaveHandler.bind(this));
            $nodeDiv.on('click', this.nodeClickHandler.bind(this));
            $nodeDiv.on('click', '.topEdge', { 'nodeData': data }, this.topEdgeClickHandler.bind(this));
            $nodeDiv.on('click', '.bottomEdge', { 'nodeData': data }, this.bottomEdgeClickHandler.bind(this));
            $nodeDiv.on('click', '.leftEdge, .rightEdge', { 'nodeData': data }, this.hEdgeClickHandler.bind(this));
            $nodeDiv.on('click', '.toggleBtn', this.toggleVNodes.bind(this));

            if (opts.draggable) {
                this.bindDragDrop($nodeDiv);
                this.touchHandled = false;
                this.touchMoved = false;
                this.touchTargetNode = null;
            }
            // allow user to append dom modification after finishing node create of orgchart
            if (opts.createNode) {
                opts.createNode($nodeDiv, data);
            }

            return $nodeDiv;
        },
        // recursively build the tree
        buildHierarchy: function ($appendTo, data) {
            var that = this;
            var opts = this.options;
            var level = 0;
            if (data.level) {
                level = data.level;
            } else {
                level = data.level = $appendTo.parentsUntil('.orgchart', '.nodes').length + 1;
            }
            // Construct the node
            var childrenData = data.children;
            var hasChildren = childrenData ? childrenData.length : false;
            var $nodeWrapper;
            if (Object.keys(data).length > 2) {
                var $nodeDiv = this.createNode(data);
                if (opts.verticalLevel && level >= opts.verticalLevel) {
                    $appendTo.append($nodeDiv);
                }else {
                    $nodeWrapper = $('<table>');
                    $appendTo.append($nodeWrapper.append($('<tr/>').append($('<td' + (hasChildren ? ' colspan="' + childrenData.length * 2 + '"' : '') + '></td>').append($nodeDiv))));
                }
            }
            // Construct the lower level(two "connectiong lines" rows and "inferior nodes" row)
            if (hasChildren) {
                var isHidden = (level + 1 > opts.visibleLevel || data.collapsed) ? ' hidden' : '';
                var isVerticalLayer = (opts.verticalLevel && (level + 1) >= opts.verticalLevel) ? true : false;
                var $nodesLayer;
                if (isVerticalLayer) {
                    $nodesLayer = $('<ul>');
                    if (isHidden && level + 1 > opts.verticalLevel) {
                        $nodesLayer.addClass(isHidden);
                    }
                    if (level + 1 === opts.verticalLevel) {
                        $appendTo.children('table').append('<tr class="verticalNodes' + isHidden + '"><td></td></tr>')
                            .find('.verticalNodes').children().append($nodesLayer);
                    } else {
                        $appendTo.append($nodesLayer);
                    }
                } else {
                    var $upperLines = $('<tr class="lines' + isHidden + '"><td colspan="' + childrenData.length * 2 + '"><div class="downLine"></div></td></tr>');
                    var lowerLines = '<tr class="lines' + isHidden + '"><td class="rightLine"></td>';
                    for (var i=1; i<childrenData.length; i++) {
                        lowerLines += '<td class="leftLine topLine"></td><td class="rightLine topLine"></td>';
                    }
                    lowerLines += '<td class="leftLine"></td></tr>';
                    $nodesLayer = $('<tr class="nodes' + isHidden + '">');
                    if (Object.keys(data).length === 2) {
                        $appendTo.append($upperLines).append(lowerLines).append($nodesLayer);
                    } else {
                        $nodeWrapper.append($upperLines).append(lowerLines).append($nodesLayer);
                    }
                }
                // recurse through children nodes
                $.each(childrenData, function () {
                    var $nodeCell = isVerticalLayer ? $('<li>') : $('<td colspan="2">');
                    $nodesLayer.append($nodeCell);
                    this.level = level + 1;
                    that.buildHierarchy($nodeCell, this);
                });
            }
        },
        // build the child nodes of specific node
        buildChildNode: function ($appendTo, data) {
            $appendTo.find('td:first').attr('colspan', data.length * 2);
            this.buildHierarchy($appendTo, { 'children': data });
        },
        // exposed method
        addChildren: function ($node, data) {
            this.buildChildNode($node.closest('table'), data);
            if (!$node.children('.bottomEdge').length) {
                $node.append('<i class="edge verticalEdge bottomEdge fa"></i>');
            }
            if (!$node.find('.symbol').length) {
                $node.children('.title').prepend('');
            }
            if (this.isInAction($node)) {
                this.switchVerticalArrow($node.children('.bottomEdge'));
            }
        },
        // build the parent node of specific node
        buildParentNode: function ($currentRoot, data) {
            data.relationship = data.relationship || '001';
            var $table = $('<table>')
                .append($('<tr>').append($('<td colspan="2">').append(this.createNode(data))))
                .append('<tr class="lines"><td colspan="2"><div class="downLine"></div></td></tr>')
                .append('<tr class="lines"><td class="rightLine"></td><td class="leftLine"></td></tr>');
            this.$chart.prepend($table)
                .children('table:first').append('<tr class="nodes"><td colspan="2"></td></tr>')
                .children('tr:last').children().append(this.$chart.children('table').last());
        },
        // exposed method
        addParent: function ($currentRoot, data) {
            this.buildParentNode($currentRoot, data);
            if (!$currentRoot.children('.topEdge').length) {
                $currentRoot.children('.title').after('<i class="edge verticalEdge topEdge fa"></i>');
            }
            if (this.isInAction($currentRoot)) {
                this.switchVerticalArrow($currentRoot.children('.topEdge'));
            }
        },
        // subsequent processing of build sibling nodes
        complementLine: function ($oneSibling, siblingCount, existingSibligCount) {
            var lines = '';
            for (var i = 0; i < existingSibligCount; i++) {
                lines += '<td class="leftLine topLine"></td><td class="rightLine topLine"></td>';
            }
            $oneSibling.parent().prevAll('tr:gt(0)').children().attr('colspan', siblingCount * 2)
                .end().next().children(':first').after(lines);
        },
        // build the sibling nodes of specific node
        buildSiblingNode: function ($nodeChart, data) {
            var newSiblingCount = $.isArray(data) ? data.length : data.children.length;
            var existingSibligCount = $nodeChart.parent().is('td') ? $nodeChart.closest('tr').children().length : 1;
            var siblingCount = existingSibligCount + newSiblingCount;
            var insertPostion = (siblingCount > 1) ? Math.floor(siblingCount/2 - 1) : 0;
            // just build the sibling nodes for the specific node
            if ($nodeChart.parent().is('td')) {
                var $parent = $nodeChart.closest('tr').prevAll('tr:last');
                $nodeChart.closest('tr').prevAll('tr:lt(2)').remove();
                this.buildChildNode($nodeChart.parent().closest('table'), data);
                var $siblingTds = $nodeChart.parent().closest('table').children('tr:last').children('td');
                if (existingSibligCount > 1) {
                    this.complementLine($siblingTds.eq(0).before($nodeChart.closest('td').siblings().addBack().unwrap()), siblingCount, existingSibligCount);
                } else {
                    this.complementLine($siblingTds.eq(insertPostion).after($nodeChart.closest('td').unwrap()), siblingCount, 1);
                }
            } else { // build the sibling nodes and parent node for the specific ndoe
                this.buildHierarchy($nodeChart.closest('.orgchart'), data);
                this.complementLine($nodeChart.next().children('tr:last').children().eq(insertPostion).after($('<td colspan="2">').append($nodeChart)),
                    siblingCount, 1);
            }
        },
        //
        addSiblings: function ($node, data) {
            this.buildSiblingNode($node.closest('table'), data);
            $node.closest('.nodes').data('siblingsLoaded', true);
            if (!$node.children('.leftEdge').length) {
                $node.children('.topEdge').after('<i class="edge horizontalEdge rightEdge fa"></i><i class="edge horizontalEdge leftEdge fa"></i>');
            }
            if (this.isInAction($node)) {
                this.switchHorizontalArrow($node);
                $node.children('.topEdge').removeClass('fa-chevron-up').addClass('fa-chevron-down');
            }
        },
        //
        removeNodes: function ($node) {
            var $parent = $node.closest('table').parent();
            var $sibs = $parent.parent().siblings();
            if ($parent.is('td')) {
                if (this.getNodeState($node, 'siblings').exist) {
                    $sibs.eq(2).children('.topLine:lt(2)').remove();
                    $sibs.slice(0, 2).children().attr('colspan', $sibs.eq(2).children().length);
                    $parent.remove();
                } else {
                    $sibs.eq(0).children().removeAttr('colspan')
                        .find('.bottomEdge').remove()
                        .end().end().siblings().remove();
                }
            } else {
                $parent.add($parent.siblings()).remove();
            }
        },
        //
        export: function (exportFilename, exportFileextension) {
            var that = this;
            exportFilename = (typeof exportFilename !== 'undefined') ?  exportFilename : this.options.exportFilename;
            exportFileextension = (typeof exportFileextension !== 'undefined') ?  exportFileextension : this.options.exportFileextension;
            if ($(this).children('.spinner').length) {
                return false;
            }
            var $chartContainer = this.$chartContainer;
            var $mask = $chartContainer.find('.mask');
            if (!$mask.length) {
                $chartContainer.append('<div class="mask"><i class="fa fa-circle-o-notch fa-spin spinner"></i></div>');
            } else {
                $mask.removeClass('hidden');
            }
            var sourceChart = $chartContainer.addClass('canvasContainer').find('.orgchart:not(".hidden")').get(0);
            var flag = that.options.direction === 'l2r' || that.options.direction === 'r2l';
            html2canvas(sourceChart, {
                'width': flag ? sourceChart.clientHeight : sourceChart.clientWidth,
                'height': flag ? sourceChart.clientWidth : sourceChart.clientHeight,
                'onclone': function (cloneDoc) {
                    $(cloneDoc).find('.canvasContainer').css('overflow', 'visible')
                        .find('.orgchart:not(".hidden"):first').css('transform', '');
                },
                'onrendered': function (canvas) {
                    $chartContainer.find('.mask').addClass('hidden');


                    $("#tablainfo").remove();
                    $("#tituloger").remove();
                    if (exportFileextension.toLowerCase() === 'pdf') {
                        var doc = {};
                        var docWidth = Math.floor(canvas.width * 0.2646);
                        var docHeight = Math.floor(canvas.height * 0.2646);
                        if (docWidth > docHeight) {
                            doc = new jsPDF('l', 'mm', [docWidth, docHeight]);
                        } else {
                            doc = new jsPDF('p', 'mm', [docHeight, docWidth]);
                        }
                        doc.addImage(canvas.toDataURL(), 'png', 0, 0);
                        doc.save(exportFilename + '.pdf');
                    } else {
                        var isWebkit = 'WebkitAppearance' in document.documentElement.style;
                        var isFf = !!window.sidebar;
                        var isEdge = navigator.appName === 'Microsoft Internet Explorer' || (navigator.appName === "Netscape" && navigator.appVersion.indexOf('Edge') > -1);

                        if ((!isWebkit && !isFf) || isEdge) {
                            window.navigator.msSaveBlob(canvas.msToBlob(), exportFilename + '.png');
                        } else {
                            var selector = '.oc-download-btn' + (that.options.chartClass !== '' ? '.' + that.options.chartClass : '');
                            if (!$chartContainer.find(selector).length) {
                                $chartContainer.append('<a class="oc-download-btn' + (that.options.chartClass !== '' ? ' ' + that.options.chartClass : '') + '"'
                                    + ' download="' + exportFilename + '.png"></a>');
                            }
                            $chartContainer.find(selector).attr('href', canvas.toDataURL())[0].click();
                        }
                    }
                }
            })
                .then(function () {
                    $chartContainer.removeClass('canvasContainer');
                }, function () {
                    $chartContainer.removeClass('canvasContainer');
                });
        }
    };

    $.fn.orgchart = function (opts) {
        return new OrgChart(this, opts).init();
    };

}));
