async function apiCall(endpoint, data, _options) {
  var action = data['action']
  if (!action) {
    try {
      action = data.get('action')
    } catch (e) {}
  }
  endpoint = endpoint || null
  data = data || {}
  _options = _options || {}
  var defaults = {
    url: 'index.php?page=structure&rex-api-call=' + endpoint,
    type: 'POST',
    cache: false
  }
  var options = $.extend({}, defaults, _options)
  options.data = data
  try {
    return await $.ajax(options)
  } catch (error) {
    return error.responseJSON ? error.responseJSON : false
  }
}

$(document).on('rex:ready', function (e, container) {
  let reloadWindowOnClose = false
  const $modal = $('#modal-massif-dnd')
  $modal.on('hidden.bs.modal', function () {
    $(this).find('.modal-body').html('')
    if (reloadWindowOnClose) {
      window.location.reload()
    }
  })
  $('[data-massif-dnd-modal]')
    .off('.massif-dnd-modal')
    .on('click.massif-dnd-modal', async function () {
      const action = $(this).data('action')
      const data_id = $(this).data('id')
      const tableName = $(this).data('table-name')
      reloadWindowOnClose = $(this).data('reload-window-on-close')

      const data = await apiCall('massif_dnd', {
        action,
        data_id,
        table_name: tableName
      })

      $modal.find('.modal-body').html(data)
      $modal.modal('show')
    })
})
