;(function ($, _) {

  'use strict';

  /**
   * extract $root.id suffix in order to get the $element position in menu tree
   * @param $root
   * @returns String
   */
  function generateIdentifier() {
    return _.random(1, 10000).toString() +  _.now().toString() + _.random(1, 10000).toString();
  }

  /**
   * create a new jQuery instance of `proto` element with substited `pos` value
   * @param proto
   * @returns {Function}
   */
  function createElement(proto) {
    return function (pos) {
      return $(proto.replace(/__name__/g, pos));
    };
  }

  /**
   * update bootstrap `cols-*` classes for a deep nested menus display
   * @param level
   * @returns {Function}
   */
  function setLevelMarkup(level, maxLevel) {
    return function ($elt) {
      var $f = $elt.find('.form-content');

      $elt.attr('data-level', level);

      if (level >= maxLevel) {
        level = maxLevel;
        $f.find('.actions').find('button.add-page-menu').prop('disabled', true);
      }

      // add bootstrap column for level indentation
      $f.find('.level-indicator').addClass('col-md-' + level);
      $f.find('.actions')
        .removeClass('col-md-6')
        .addClass('col-md-' + (maxLevel - level + 1));

      return $elt;
    };
  }

  /**
   * update html attributes `name` and `id` with an unique indentifier for the newly inserted element
   * @param options
   * @returns {Function}
   */
  function updateIdentifiersAttributes(options) {
    return function ($elt) {
      var identifier = generateIdentifier(),
          updater = updateAttributes(identifier),
          $fields = [
            $elt,
            $elt.find(options.fieldLabelSelector),
            $elt.find(options.fieldPageSelector),
            $elt.find(options.fieldParentSelector),
            $elt.find(options.fieldPositionSelector),
            $elt.find(options.fieldIdSelector)
          ]
        ;

      // update all fields attributes
      _.forEach($fields, function ($elt) {
        updater($elt);
      });

      // use `attr` modifier instead of using `data` jQuery function because we need to use
      // `data-menu-page-id` expression as a jQuery selector
      $elt
        .attr('data-menu-page-id', identifier)
        .find(options.fieldIdSelector).val(identifier)
      ;

      return $elt;
    };
  }

  /**
   * update `position` value for each attribute in a given element $e
   * @param pos the position to set
   * @returns {Function}
   */
  function updateAttributes(pos) {
    return function ($elt) {
      var pId = /_data_menu_tree_(\d+-?)+/g,
        pName = /\[data\]\[menu_tree\]\[(\d+-?)+\]/g,
        newId = '_data_menu_tree_' + pos,
        newName = '[data][menu_tree][' + pos + ']'
        ;

      $elt.attr('id', $elt.attr('id').replace(pId, newId));

      if ($elt.attr('name')) {
        $elt.attr('name', $elt.attr('name').replace(pName, newName));
      }

      return $elt;
    };
  }

  /**
   *
   * @param $container
   * @returns {Function}
   */
  function appendMenuItem($root, options) {
    var findDeepPreviousSibling = (function ($root) {
      var deepSearch = function (parentId) {
        var $previousSibling = $root.siblings('[data-parent="' + parentId + '"]').last(),
            previousSiblingId = $previousSibling.data('menu-page-id');

        if ($root.siblings('[data-parent="' + previousSiblingId + '"]').size()) {
          return deepSearch(previousSiblingId);
        }

        return $previousSibling;
      };

      return deepSearch;
    })($root);

    return function ($elt) {
      var parentId = $root.find(options.fieldIdSelector).val(),
        $prevRoot = findDeepPreviousSibling(parentId);

      if (!$prevRoot.size()) {
        $prevRoot = $root;
      }

      // set attr instead of using `$.data` function because we need to use
      // `[data-parent="{parentId}"]` expression as a selector
      $elt.attr('data-parent', parentId);
      $prevRoot.after($elt);

      // `$elt` does not mean anything now, so we must return the newly inserted element
      return $root.siblings('#' + $elt.attr('id'));
    };
  }

  /**
   *
   * @param selector
   * @returns {Function}
   */
  function remove($item) {
    var parentId = $item.data('menu-page-id');

    // recursively remove all children menus referencing $item
    $item
      .parent()
      .find('[data-parent="' + parentId + '"]')
      .not('[data-menu-page-id="' + parentId + '"]')
      .each(function () {
        remove($(this));
      });

    return $item.remove();
  }

  /**
   * update `position` information for each field `id` and `name` attributes
   * @returns {Function}
   * @param $root
   * @param options
   */
  function updateChildrenPosition($root, options) {
    var parentId = $root.data('menu-page-id');

    return $root.siblings('[data-parent="' + parentId + '"]').each(function (pos) {
      $(this).find(options.fieldPositionSelector).val(pos);
      $(this).find(options.fieldParentSelector).val(parentId);
    });
  }

  /**
   *
   * @param options
   * @param defaultLevel
   * @returns {Function}
   */
  function initBehavior(options, defaultLevel) {
    return function ($root) {
      var level = (defaultLevel || $root.data('level')) + 1,
          addMenuItem = _.flow(
            createElement(options.eltPrototype, level),
            setLevelMarkup(level, options.maxLevel),
            updateIdentifiersAttributes(options),
            appendMenuItem($root, options),
            initBehavior(options, level)
          );

      $root
        .find('> .form-content')
        .on('click', 'button.add-page-menu', function (e) {
          e.preventDefault();
          addMenuItem($root.siblings().size() + 1);
          // update position for each sibling element
          updateChildrenPosition($root, options)
        })
        .on('click', 'button.remove-page-menu', function (e) {
          e.preventDefault();
          var parentId = $root.data('parent'),
              $parent = $root.siblings('[data-menu-page-id="' + parentId + '"]').first();
          remove($root);
          // update position for each sibling element
          updateChildrenPosition($parent, options)
        });

      return $root;
    };
  }

  /**
   *
   * @param options
   * @param $root
   * @returns {*}
     */
  function initAddFirstLevel($root, $addMenuButton, options) {
    var level =  0,
      addMenuItem = _.flow(
        createElement(options.eltPrototype, level),
        setLevelMarkup(level, options.maxLevel),
        updateIdentifiersAttributes(options),
        function ($elt) {
          // first level doesn't have parent -> the parentId is the same as the menu id
          var parentId = $elt.data('menu-page-id');
          $elt.attr('data-parent', parentId);
          $elt.find(options.fieldParentSelector).val(parentId);
          $root.append($elt);

          return $elt;
        },
        initBehavior(options)
      );

    $addMenuButton
      .on('click', function (e) {
        e.preventDefault();
        addMenuItem($root.find(options.itemSelector).size() + 1);
      });

    return $root;
  }


  /**
   * jQuery plugin for binding multi-level behavior to page_menu html form
   * @returns {jQuery}
   */
  $.fn.synapsePageMenu = function (opts) {

    var level = 1,
      options = _.assign({
      eltPrototypeSelector: null,
      itemSelector: '.synapse-page-menu-item',
      fieldLabelSelector: '.page-menu-item-label',
      fieldPageSelector: '.page-menu-item-page',
      fieldPositionSelector: '.page-menu-item-position',
      fieldParentSelector: '.page-menu-item-parent',
      fieldIdSelector: '.page-menu-item-id',
      addFirstLevelSelector: '.add-first-level-page-menu',
      maxLevel: 5
    }, opts);

    if (!options.eltPrototypeSelector) {
      throw new Error('menu_page prototype must be provided');
    }

    $(this).not('.page-menu-initialized').each(function () {
      var opts = _.assign({}, options),
          initialize = initBehavior(opts);

      opts.eltPrototype = $(this).find(opts.eltPrototypeSelector).data('prototype');

      // init available values
      $(this).find(opts.itemSelector).each(function () {
        initialize($(this));
      });

      // init the add first level menu button
      initAddFirstLevel(
        $(opts.eltPrototypeSelector),
        $(this).find(opts.addFirstLevelSelector),
        opts
      );

      $(this).addClass('page-menu-initialized');
    });

    return this;
  };

} (jQuery, _));
