var org_id;

$j(function(){ 

    $j('#org_id').change(function(){ 
        var $js = $j(this);
        org_id = $js.val();
                    
        $j('#bottom, #p2').hide();
        
        if ($js.val() != '') {
            $j.ajax({
                url: 'ajax_data.php?object=borrarMasivo4w&org_id=' + org_id,
                dataType: 'json',
                success: function(json) {
                    var html = '';
                    var j;
                    for (var j=0; j < json.length; j++) {
                        
                        var y = json[j];
                        var id = 'y_' + y;

                        html += '<li class="y"><input type="checkbox" id="'+id+'" value="'+y+'" />&nbsp;<label class="inline" for="' + id + '">'+y+'</label></li>';
                    }

                    $j('#p2_ul').html(html);
                    $j('#bottom, #p2').show();
                    $j('#exito').hide();
                }
            });
        }
    });

    $j('#submit').click(function(){
         var ys = '';

         var vals = [];
         $j('input:checked').each(function() {
            vals.push($j(this).val());
         });

         ys = vals.join(',');

         if (ys == '') {
             alert('Seleccione los años!');
         }
         else {
            if (confirm('Esta seguro?')) {
                $j.ajax({
                    url: 'ajax_data.php?object=borrarMasivo4w&org_id=' + org_id + '&ys=' + ys,
                    success: function() {
                        $j('#exito').show();
                    }
                });
            }
         }
    });
});

