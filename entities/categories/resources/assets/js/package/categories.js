import Swal from 'sweetalert2';

export let categories = {
  initList: () => {
    let $ = window.$;

    $('.categories-package .categories-list').each(function() {
      let orderURL = $(this).attr('data-order-url'),
          orderDataTarget = $(this).attr('data-order-serialize');

      $(this).nestable({
        'dragClass': 'categories-package dd-dragel',
        group: 1,
      }).on('change', function(e) {
        let list = e.length ? e : $(e.target);

        let data = {
          data: window.JSON.stringify(list.nestable('serialize')),
        };

        if (typeof orderDataTarget !== 'undefined') {
          $(orderDataTarget).val(data.data);
        }

        if (typeof orderURL === 'undefined') {
          return true;
        }

        $.ajax({
          'url': orderURL,
          'type': 'POST',
          'data': data,
          'dataType': 'json',
          'success': function(data) {
            if (data.success) {
              Swal.fire({
                title: 'Порядок изменен',
                icon: 'success'
              });
            } else {
              Swal.fire({
                title: 'Ошибка',
                text: 'При изменении порядка произошла ошибка',
                icon: 'error',
              });
            }
          },
          'error': function() {
            Swal.fire({
              title: 'Ошибка',
              text: 'При изменении порядка произошла ошибка',
              icon: 'error',
            });
          },
        });
      });

      $(this).nestable('collapseAll');
    });
  },
  initTree: () => {
    $('.categories-package .categories-tree').each(function() {
      let list = $(this),
          targetField = list.attr('data-target');

      let options = {
        'core': {
          'check_callback': true,
          'multiple': (list.attr('data-multiple') === 'true'),
        },
        'plugins': ['types', 'checkbox'],
        'types': {
          'default': {
            'icon': 'fa fa-folder',
          },
        },
        'checkbox': {
          'three_state': false,
        },
      };

      list.jstree(options).on('changed.jstree', function(node, action) {

        if (list.attr('data-cascade') === 'up') {
          if (typeof action.node !== 'undefined') {
            $.each(action.node.parents, function(key, val) {
              if (action.instance.get_checked_descendants(val).length > 0) {
                action.instance.check_node(val);
              } else {
                action.instance.uncheck_node(val);
              }
            });
          }
        }

        let ids = action.instance.get_selected();
        $('input[name=' + targetField + ']').val(ids);
      }).on('ready.jstree', function(tree) {
        if (typeof list.attr('data-selected') !== 'undefined' &&
            list.attr('data-selected') !== '') {
          let selected = list.attr('data-selected').split(',');
          list.jstree().uncheck_all();
          list.jstree().check_node(selected);
        }
      });
    });
  }
};
