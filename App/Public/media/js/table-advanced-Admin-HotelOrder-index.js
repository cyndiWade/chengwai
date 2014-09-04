var TableAdvanced = function () {

    var initTable1 = function() {

        /* Formating function for row details */
        function fnFormatDetails ( oTable, nTr )
        {
            var aData = oTable.fnGetData( nTr );
  
            var sOut = '<table>';
            sOut += '<tr><td>酒店名称:</td><td>'+aData[2]+'</td></tr>';
            sOut += '<tr><td>预定房型:</td><td>'+aData[3]+'</td></tr>';
            sOut += '<tr><td>入住人:</td><td>'+aData[4]+'</td></tr>';
            sOut += '</table>';
             
            return sOut;
        }

        /*
         * Insert a 'details' column to the table
         */
        var nCloneTh = document.createElement( 'th' );
        var nCloneTd = document.createElement( 'td' );
        nCloneTd.innerHTML = '<span class="row-details row-details-close"></span>';
         
        $('#sample_1 thead tr').each( function () {
            this.insertBefore( nCloneTh, this.childNodes[0] );
        } );
         
        $('#sample_1 tbody tr').each( function () {
            this.insertBefore(  nCloneTd.cloneNode( true ), this.childNodes[0] );
        } );
         
        /*
         * Initialse DataTables, with no sorting on the 'details' column
         */
        var oTable = $('#sample_1').dataTable( {
//			"aoColumns" : [
//				null,
//			 	 { "bSortable": true },
//				 { "bSortable": true },
//				{ "bSortable": true },
//				{ "bSortable": true },
//				 { "bSortable": true },
//				 { "bSortable": false },
//				 { "bSortable": false },
//				 { "bSortable": false },
//				 { "bSortable": true },
//				 { "bSortable": false }
//			],
            "aoColumnDefs": [
                {"bSortable": false, "aTargets": [ 0 ] }

            ],
            "oLanguage": {
        		"sLengthMenu": "_MENU_ 每页条数"
        	},
            "aaSorting": [[10, 'desc']],		//排序
             "aLengthMenu": [
                [10, 20, 30, -1],
                [10, 20, 30, "所有"] // change per page values here
            ],
            // set the initial value
            "iDisplayLength": 10,
        });

        jQuery('#sample_1_wrapper .dataTables_filter input').addClass("m-wrap small"); // modify table search input
        jQuery('#sample_1_wrapper .dataTables_length select').addClass("m-wrap small"); // modify table per page dropdown
        jQuery('#sample_1_wrapper .dataTables_length select').select2(); // initialzie select2 dropdown
         
        /* Add event listener for opening and closing details
         * Note that the indicator for showing which row is open is not controlled by DataTables,
         * rather it is done here
         */
        $('#sample_1').on('click', ' tbody td .row-details', function () {
            var nTr = $(this).parents('tr')[0];
            if ( oTable.fnIsOpen(nTr) )
            {
                /* This row is already open - close it */
                $(this).addClass("row-details-close").removeClass("row-details-open");
                oTable.fnClose( nTr );
            }
            else
            {
                /* Open this row */                
                $(this).addClass("row-details-open").removeClass("row-details-close");
                oTable.fnOpen( nTr, fnFormatDetails(oTable, nTr), 'details' );
            }
        });
    }

  

    return {

        //main function to initiate the module
        init: function () {
            
            if (!jQuery().dataTable) {
                return;
            }

            initTable1();

        }

    };

}();