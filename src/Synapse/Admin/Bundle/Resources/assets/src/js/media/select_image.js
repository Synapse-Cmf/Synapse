// custom select widget for images
$(() => {
  // select2 plugin
  $('.image_choice_list')
    .select2({ theme: 'bootstrap', minimumResultsForSearch: 3 })
    .on('select2:select', function(e){
      $(this).append($(e.params.data.element)).trigger('change.select2');
    })
  ;
});
