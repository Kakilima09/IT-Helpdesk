
<table class="table table-bordered border-bottom text-nowraps w-100 ticketdeleterow" id="supportticket-dashe">
    <thead>
        <tr >
            <th  class="wpx-40 text-center"><?php echo e(lang('Sl.No')); ?></th>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Ticket Delete')): ?>

            <th class="wpx-40 text-center">
                <input type="checkbox"  id="customCheckAll">
                <label  for="customCheckAll"></label>
            </th>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->denies('Ticket Delete')): ?>

            <th class="wpx-40 text-center">
                <input type="checkbox"  id="customCheckAll" disabled>
                <label  for="customCheckAll"></label>
            </th>
            <?php endif; ?>

            <th ><?php echo e(lang('Ticket Details')); ?></th>
            <th><?php echo e(lang('User')); ?></th>
            <th ><?php echo e(lang('Status')); ?></th>
            <th ><?php echo e(lang('Assign To')); ?></th>
            <th ><?php echo e(lang('Actions')); ?></th>

        </tr>
    </thead>
    <tbody id="refresh">
            <?php $i = 1 ?>
        <?php $__currentLoopData = $alltickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tickets): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if($tickets->myassignuser_id == null && $tickets->selfassignuser_id == null): ?>

        <tr <?php echo e($tickets->replystatus == 'Replied'? 'class=bg-success-transparent': ''); ?>>
            <td class="wpx-40 text-center">
                <?php echo e($i++); ?>

            </td>
            <td class="wpx-40 text-center">
                <?php if(Auth::user()->can('Ticket Delete')): ?>
                    <input type="checkbox" name="student_checkbox[]" class="checkall" value="<?php echo e($tickets->id); ?>" />
                <?php else: ?>
                    <input type="checkbox" name="student_checkbox[]" class="checkall" value="<?php echo e($tickets->id); ?>" disabled />
                <?php endif; ?>
            </td>
            <td class="overflow-hidden ticket-details">
                <div class="d-flex align-items-center">
                    <div class="">
                        <?php if($tickets->ticketnote->isEmpty()): ?>
                            <?php if($tickets->overduestatus != null): ?>

                            <div class="ribbon ribbon-top-right1 text-danger">
                                <span class="bg-danger text-white"><?php echo e($tickets->overduestatus); ?></span>
                            </div>

                            <?php endif; ?>
                        <?php else: ?>

                            <div class="ribbon ribbon-top-right text-warning">
                                <span class="bg-warning text-white"><?php echo e(lang('Note')); ?></span>
                            </div>

                            <?php if($tickets->overduestatus != null): ?>
                            <div class="ribbon ribbon-top-right1 text-danger">
                                <span class="bg-danger text-white"><?php echo e($tickets->overduestatus); ?></span>
                            </div>
                            <?php endif; ?>

                        <?php endif; ?>

                        <a href="<?php echo e(url('admin/ticket-view/' . $tickets->ticket_id)); ?>" class="fs-14 d-block font-weight-semibold"><?php echo e($tickets->subject); ?></a>

                        <ul class="fs-13 font-weight-normal d-flex custom-ul">
                            <li class="pe-2 text-muted">#<?php echo e($tickets->ticket_id); ?></span>
                            <li class="px-2 text-muted" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo e(lang('Date')); ?>"><i class="fe fe-calendar me-1 fs-14"></i> <?php echo e($tickets->created_at->timezone(Auth::user()->timezone)->format(setting('date_format'))); ?></li>

                            <?php if($tickets->priority != null): ?>
                                <?php if($tickets->priority == "Low"): ?>
                                    <li class="ps-5 pe-2 preference preference-low" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo e(lang('Priority')); ?>"><?php echo e(lang($tickets->priority)); ?></li>

                                <?php elseif($tickets->priority == "High"): ?>
                                    <li class="ps-5 pe-2 preference preference-high" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo e(lang('Priority')); ?>"><?php echo e(lang($tickets->priority)); ?></li>

                                <?php elseif($tickets->priority == "Critical"): ?>
                                    <li class="ps-5 pe-2 preference preference-critical" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo e(lang('Priority')); ?>"><?php echo e(lang($tickets->priority)); ?></li>

                                <?php else: ?>
                                    <li class="ps-5 pe-2 preference preference-medium" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo e(lang('Priority')); ?>"><?php echo e(lang($tickets->priority)); ?></li>
                                <?php endif; ?>
                            <?php else: ?>
                                ~
                            <?php endif; ?>

                            <?php if($tickets->category_id != null): ?>
                                <?php if($tickets->category != null): ?>

                                <li class="px-2 text-muted" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo e(lang('Category')); ?>"><i class="fe fe-grid me-1 fs-14" ></i><?php echo e(Str::limit($tickets->category->name, '40')); ?></li>

                                <?php else: ?>

                                ~
                                <?php endif; ?>
                            <?php else: ?>

                                ~
                            <?php endif; ?>

                            <?php if($tickets->last_reply == null): ?>
                                <li class="px-2 text-muted" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo e(lang('Last Replied')); ?>"><i class="fe fe-clock me-1 fs-14"></i><?php echo e($tickets->created_at->diffForHumans()); ?></li>

                            <?php else: ?>
                            <li class="px-2 text-muted" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo e(lang('Last Replied')); ?>"><i class="fe fe-clock me-1 fs-14"></i><?php echo e($tickets->last_reply->diffForHumans()); ?></li>

                            <?php endif; ?>

                            <?php if($tickets->purchasecodesupport != null): ?>
                            <?php if($tickets->purchasecodesupport == 'Supported'): ?>

                            <li class="px-2 text-success font-weight-semibold"><?php echo e(lang('Support Active')); ?></li>
                            <?php endif; ?>
                            <?php if($tickets->purchasecodesupport == 'Expired'): ?>

                            <li class="px-2 text-danger-dark font-weight-semibold"><?php echo e(lang('Support Expired')); ?></li>
                            <?php endif; ?>
                            <?php endif; ?>

                        </ul>
                    </div>
                </div>
            </td>
            <td>
                <?php echo e($tickets->cust->username); ?> (<?php echo e(lang($tickets->cust->userType)); ?>)
            </td>
            <td>
                <?php if($tickets->status == "New"): ?>

                <span class="badge badge-burnt-orange"><?php echo e(lang($tickets->status)); ?></span>

                <?php elseif($tickets->status == "Re-Open"): ?>

                <span class="badge badge-teal"><?php echo e(lang($tickets->status)); ?></span>

                <?php elseif($tickets->status == "Inprogress"): ?>

                <span class="badge badge-info"><?php echo e(lang($tickets->status)); ?></span>

                <?php elseif($tickets->status == "On-Hold"): ?>

                <span class="badge badge-warning"><?php echo e(lang($tickets->status)); ?></span>

                <?php else: ?>

                <span class="badge badge-danger"><?php echo e(lang($tickets->status)); ?></span>

                <?php endif; ?>
            </td>
            <td>
                <?php if(Auth::user()->can('Ticket Assign')): ?>
                    <?php if($tickets->status == 'Suspend' || $tickets->status == 'Closed'): ?>
                        <div class="btn-group">
                            <?php if($tickets->ticketassignmutliples->isNotEmpty() && $tickets->selfassignuser_id == null): ?>

                            <button class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" disabled><?php echo e(lang('Multi Assign')); ?> <span class="caret"></span></button>
                            <button data-id="<?php echo e($tickets->id); ?>" class="btn btn-outline-primary" id="btnremove" disabled data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="<?php echo e(lang('Unassign')); ?>" aria-label="Unassign"><i class="fe fe-x"></i></button>
                            <?php elseif($tickets->ticketassignmutliples->isEmpty() && $tickets->selfassignuser_id != null): ?>

                            <button class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown"  disabled><?php echo e($tickets->selfassign->name); ?> (self) <span class="caret"></span></button>
                            <button data-id="<?php echo e($tickets->id); ?>" class="btn btn-outline-primary" id="btnremove" disabled data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="<?php echo e(lang('Unassign')); ?>" aria-label="Unassign"><i class="fe fe-x"></i></button>
                            <?php else: ?>

                            <button class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown"  disabled><?php echo e(lang('Assign')); ?><span class="caret"></span></button>
                            <?php endif; ?>

                        </div>
                    <?php else: ?>
                        <?php if($tickets->ticketassignmutliples->isEmpty() && $tickets->selfassignuser_id == null): ?>

                            <div class="btn-group">
                                <button class="btn btn-outline-primary dropdown-toggle btn-sm" data-bs-toggle="dropdown"><?php echo e(lang('Assign')); ?> <span class="caret"></span></button>
                                <ul class="dropdown-menu" role="menu">
                                    <li class="dropdown-plus-title"><?php echo e(lang('Assign')); ?> <b aria-hidden="true" class="fa fa-angle-up"></b></li>
                                    <li>
                                        <a href="javascript:void(0);" id="selfassigid" data-id="<?php echo e($tickets->id); ?>"><?php echo e(lang('Self Assign')); ?></a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0)" data-id="<?php echo e($tickets->id); ?>" id="assigned">
                                        <?php echo e(lang('Other Assign')); ?>

                                        </a>
                                    </li>
                                </ul>
                            </div>
                        <?php else: ?>
                            <div class="btn-group">
                                <?php if($tickets->ticketassignmutliples->isNotEmpty() && $tickets->selfassignuser_id == null): ?>
                                    <?php if($tickets->ticketassignmutliples->isEmpty() && $tickets->selfassign == null): ?>
                                    <button class="btn btn-outline-primary dropdown-toggle btn-sm" data-bs-toggle="dropdown"><?php echo e(lang('Assign')); ?> <span class="caret"></span></button>
                                    <?php else: ?>
                                    <button class="btn btn-outline-primary dropdown-toggle btn-sm" data-bs-toggle="dropdown"><?php echo e(lang('Multi Assign')); ?> <span class="caret"></span></button>
                                    <a href="javascript:void(0)" data-id="<?php echo e($tickets->id); ?>" class="btn btn-outline-primary btn-sm" id="btnremove" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="<?php echo e(lang('Unassign')); ?>" aria-label="Unassign"><i class="fe fe-x"></i></a>
                                    <?php endif; ?>

                                <?php elseif($tickets->ticketassignmutliples->isEmpty() && $tickets->selfassignuser_id != null): ?>
                                <?php if($tickets->ticketassignmutliples->isEmpty() && $tickets->selfassign == null): ?>
                                <button class="btn btn-outline-primary dropdown-toggle btn-sm" data-bs-toggle="dropdown"><?php echo e(lang('Assign')); ?> <span class="caret"></span></button>
                                <?php else: ?>
                                <button class="btn btn-outline-primary dropdown-toggle btn-sm" data-bs-toggle="dropdown"><?php echo e($tickets->selfassign->name); ?> (self) <span class="caret"></span></button>
                                <a href="javascript:void(0)" data-id="<?php echo e($tickets->id); ?>" class="btn btn-outline-primary btn-sm" id="btnremove" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="<?php echo e(lang('Unassign')); ?>" aria-label="Unassign"><i class="fe fe-x"></i></a>
                                <?php endif; ?>
                                <?php else: ?>

                                <button class="btn btn-outline-primary dropdown-toggle btn-sm" data-bs-toggle="dropdown"><?php echo e(lang('Assign')); ?> <span class="caret"></span></button>
                                <?php endif; ?>

                                <ul class="dropdown-menu" role="menu">
                                    <li class="dropdown-plus-title"><?php echo e(lang('Assign')); ?> <b aria-hidden="true" class="fa fa-angle-up"></b></li>
                                    <li>
                                        <a href="javascript:void(0);" id="selfassigid" data-id="<?php echo e($tickets->id); ?>"><?php echo e(lang('Self Assign')); ?></a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0)" data-id="<?php echo e($tickets->id); ?>" id="assigned">
                                        <?php echo e(lang('Other Assign')); ?>

                                        </a>
                                    </li>
                                </ul>
                            </div>

                        <?php endif; ?>
                    <?php endif; ?>
                <?php else: ?>
                    ~
                <?php endif; ?>
            </td>
            <td>
                <?php if(Auth::user()->can('Ticket Edit')): ?>

                <a href="<?php echo e(url('admin/ticket-view/' . $tickets->ticket_id)); ?>" class="btn btn-sm action-btns edit-testimonial"><i class="feather feather-eye text-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo e(lang('Edit')); ?>"></i></a>
                <?php else: ?>
                    ~
                <?php endif; ?>
                <?php if(Auth::user()->can('Ticket Delete')): ?>
                <a href="javascript:void(0)" data-id="<?php echo e($tickets->id); ?>" class="btn btn-sm action-btns" id="show-delete" ><i class="feather feather-trash-2 text-danger" data-id="<?php echo e($tickets->id); ?>" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo e(lang('Delete')); ?>"></i></a>
                <?php else: ?>
                ~
                <?php endif; ?>
            </td>
        </tr>
        <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
</table>

<script>

// $('#supportticket-dashe').DataTable({

//     language: {
//         searchPlaceholder: search,
//         sSearch: '',

//     },
//     order:[],
//     columnDefs: [
//         { "orderable": false, "targets":[ 0,1,6] }
//     ],
// });

let prev = <?php echo json_encode(lang("Previous")); ?>;
let next = <?php echo json_encode(lang("Next")); ?>;
let nodata = <?php echo json_encode(lang("No data available in table")); ?>;
let noentries = <?php echo json_encode(lang("No entries to show")); ?>;
let showing = <?php echo json_encode(lang("showing page")); ?>;
let ofval = <?php echo json_encode(lang("of")); ?>;
let maxRecordfilter = <?php echo json_encode(lang("- filtered from ")); ?>;
let maxRecords = <?php echo json_encode(lang("records")); ?>;
let entries = <?php echo json_encode(lang("entries")); ?>;
let show = <?php echo json_encode(lang("Show")); ?>;
let search = <?php echo json_encode(lang("Search...")); ?>;
// Datatable
$('#supportticket-dashe').dataTable({
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
    order:[],
    columnDefs: [
        { "orderable": false, "targets":[ 0,1,4] }
    ],
});

$('.form-select').select2({
    minimumResultsForSearch: Infinity,
    width: '100%'
});

$('#customCheckAll').prop('checked', false);
$('.checkall').on('click', function(){
    if($('.checkall:checked').length == $('.checkall').length){
        $('#customCheckAll').prop('checked', true);
    }else{
        $('#customCheckAll').prop('checked', false);
    }
});

// Checkbox checkall
$('#customCheckAll').on('click', function() {
    $('.checkall').prop('checked', this.checked);
});

var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
}); // __________Popover
</script>
<?php /**PATH C:\xampp\htdocs\it-helpdesk\resources\views/admin/superadmindashboard/dashboardtabledata.blade.php ENDPATH**/ ?>