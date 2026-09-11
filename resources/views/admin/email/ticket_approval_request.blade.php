@endsection
@section('scripts')

<!-- INTERNAL Data tables -->
<script src="{{asset('assets/plugins/datatable/js/jquery.dataTables.min.js')}}?v=<?php echo time(); ?>"></script>
<script src="{{asset('assets/plugins/datatable/js/dataTables.bootstrap5.js')}}?v=<?php echo time(); ?>"></script>
<script src="{{asset('assets/plugins/datatable/dataTables.responsive.min.js')}}?v=<?php echo time(); ?>"></script>
<script src="{{asset('assets/plugins/datatable/responsive.bootstrap5.min.js')}}?v=<?php echo time(); ?>"></script>

<!-- INTERNAL Index js-->
<script src="{{asset('assets/js/support/support-sidemenu.js')}}?v=<?php echo time(); ?>"></script>

<script type="text/javascript">

"use strict";

// Datatable
// $('#support-articlelists').dataTable({
// 	order:[],
// 	responsive: true,
// });

let prev = {!! json_encode(lang("Previous")) !!};
let next = {!! json_encode(lang("Next")) !!};
let nodata = {!! json_encode(lang("No data available in table")) !!};
let noentries = {!! json_encode(lang("No entries to show")) !!};
let showing = {!! json_encode(lang("showing page")) !!};
let ofval = {!! json_encode(lang("of")) !!};
let maxRecordfilter = {!! json_encode(lang("- filtered from ")) !!};
let maxRecords = {!! json_encode(lang("records")) !!};
let entries = {!! json_encode(lang("entries")) !!};
let show = {!! json_encode(lang("Show")) !!};
let search = {!! json_encode(lang("Search...")) !!};
// Datatable
$('#support-articlelists').dataTable({
order:[],
responsive: true,
language: {
searchPlaceholder: search,
scrollX: "100%",
sSearch: '',
paginate: {
previous: prev,
next: next
},
emptyTable : nodata,
infoFiltered: `${maxRecordfilter} _MAX_ ${maxRecords}`,
info: `${showing} _PAGE_ ${ofval} _PAGES_`,
infoEmpty: noentries,
lengthMenu: `${show} _MENU_ ${entries} `,
},
// order:[],
// columnDefs: [
//     { "orderable": false, "targets":[ 0,1,4] }
// ],
});

// select2 js in datatable
$('.form-select').select2({
minimumResultsForSearch: Infinity,
width: '100%'
});
</script>

@endsection