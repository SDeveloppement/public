jQuery(document).ready(function(){
  		jQuery('#history_table').DataTable( {
		    ordering: true,
		    select: true,
		    "lengthMenu": [[50, -1], [50, "All"]],
		    "order": [[ 4, "desc" ]],
		} );
  	});