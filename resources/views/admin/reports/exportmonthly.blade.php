@extends('layouts.adminmaster')

@section('styles')
    <!-- INTERNAL Data table css -->
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.min.css') }}?v=<?php echo time(); ?>" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/responsive.bootstrap5.css') }}?v=<?php echo time(); ?>" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/buttonbootstrap.min.css') }}?v=<?php echo time(); ?>" rel="stylesheet" />
    
    <!-- INTERNAL Select2 css -->
    <link href="{{ asset('assets/plugins/select2/select2.min.css') }}?v=<?php echo time(); ?>" rel="stylesheet" />
@endsection

@section('content')
    <!--Page header-->
    <div class="page-header d-xl-flex d-block">
        <div class="page-leftheader">
            <h4 class="page-title">
                <i class="fa fa-file-export text-primary me-2"></i>
                {{ lang('Export Monthly Tickets') }}
            </h4>
        </div>
        <div class="page-rightheader ms-md-auto">
            <div class="d-flex align-items-end flex-wrap my-auto end-content breadcrumb-end">
                <div class="btn-list">
                    <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-primary">
                        <i class="fa fa-arrow-left me-2"></i>{{ lang('Back to Reports') }}
                    </a>
                    <a href="{{ route('admin.reports.export.all') }}" class="btn btn-success">
                        <i class="fa fa-file-excel me-2"></i>{{ lang('Export All Tickets') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!--End Page header-->

    <!--Export Form-->
    <div class="row">
        <div class="col-xl-8 col-lg-8 col-md-12">
            <div class="card">
                <div class="card-header border-bottom-0">
                    <h3 class="card-title">
                        <i class="fa fa-filter me-2"></i>{{ lang('Export Settings') }}
                    </h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.reports.export.monthly.post') }}" method="POST" id="exportForm">
                        @csrf
                        
                        <div class="row">
                            <!-- Month Selection -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">{{ lang('Select Month') }} <span class="text-danger">*</span></label>
                                    <select name="month" class="form-select" required>
                                        <option value="">{{ lang('Choose Month') }}</option>
                                        @foreach($months as $key => $month)
                                            <option value="{{ $key }}" {{ $key == $currentMonth ? 'selected' : '' }}>
                                                {{ $month }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('month')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Year Selection -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">{{ lang('Select Year') }} <span class="text-danger">*</span></label>
                                    <select name="year" class="form-select" required>
                                        <option value="">{{ lang('Choose Year') }}</option>
                                        @foreach($years as $year)
                                            <option value="{{ $year }}" {{ $year == $currentYear ? 'selected' : '' }}>
                                                {{ $year }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('year')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Format Selection -->
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">{{ lang('Export Format') }} <span class="text-danger">*</span></label>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="custom-control custom-radio custom-control-md">
                                                <input type="radio" class="custom-control-input" name="format" value="xlsx" checked>
                                                <span class="custom-control-label">Excel (.xlsx)</span>
                                            </label>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="custom-control custom-radio custom-control-md">
                                                <input type="radio" class="custom-control-input" name="format" value="csv">
                                                <span class="custom-control-label">CSV (.csv)</span>
                                            </label>
                                        </div>
                                    </div>
                                    @error('format')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Ticket Status Filter -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">{{ lang('Ticket Status') }}</label>
                                    <select name="status" class="form-select">
                                        <option value="all">{{ lang('All Statuses') }}</option>
                                        <option value="New">{{ lang('New') }}</option>
                                        <option value="Inprogress">{{ lang('In Progress') }}</option>
                                        <option value="On-Hold">{{ lang('On Hold') }}</option>
                                        <option value="Re-Open">{{ lang('Re-Open') }}</option>
                                        <option value="Closed">{{ lang('Closed') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Filters -->
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">{{ lang('Priority') }}</label>
                                    <select name="priority" class="form-select">
                                        <option value="all">{{ lang('All Priorities') }}</option>
                                        <option value="Low">{{ lang('Low') }}</option>
                                        <option value="Medium">{{ lang('Medium') }}</option>
                                        <option value="High">{{ lang('High') }}</option>
                                        <option value="Critical">{{ lang('Critical') }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">{{ lang('Department') }}</label>
                                    <select name="department_id" class="form-select">
                                        <option value="">{{ lang('All Departments') }}</option>
                                        @foreach($departments as $department)
                                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Include/Exclude Columns -->
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label">{{ lang('Include Columns') }}</label>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="custom-control custom-checkbox custom-control-md">
                                                <input type="checkbox" class="custom-control-input" name="columns[]" value="ticket_id" checked>
                                                <span class="custom-control-label">{{ lang('Ticket ID') }}</span>
                                            </label>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="custom-control custom-checkbox custom-control-md">
                                                <input type="checkbox" class="custom-control-input" name="columns[]" value="subject" checked>
                                                <span class="custom-control-label">{{ lang('Subject') }}</span>
                                            </label>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="custom-control custom-checkbox custom-control-md">
                                                <input type="checkbox" class="custom-control-input" name="columns[]" value="description" checked>
                                                <span class="custom-control-label">{{ lang('Description') }}</span>
                                            </label>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="custom-control custom-checkbox custom-control-md">
                                                <input type="checkbox" class="custom-control-input" name="columns[]" value="status" checked>
                                                <span class="custom-control-label">{{ lang('Status') }}</span>
                                            </label>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="custom-control custom-checkbox custom-control-md">
                                                <input type="checkbox" class="custom-control-input" name="columns[]" value="priority" checked>
                                                <span class="custom-control-label">{{ lang('Priority') }}</span>
                                            </label>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="custom-control custom-checkbox custom-control-md">
                                                <input type="checkbox" class="custom-control-input" name="columns[]" value="customer" checked>
                                                <span class="custom-control-label">{{ lang('Customer') }}</span>
                                            </label>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="custom-control custom-checkbox custom-control-md">
                                                <input type="checkbox" class="custom-control-input" name="columns[]" value="agent" checked>
                                                <span class="custom-control-label">{{ lang('Agent') }}</span>
                                            </label>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="custom-control custom-checkbox custom-control-md">
                                                <input type="checkbox" class="custom-control-input" name="columns[]" value="created_at" checked>
                                                <span class="custom-control-label">{{ lang('Created Date') }}</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Preview Section -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            <i class="fa fa-eye me-2"></i>{{ lang('Export Preview') }}
                                        </h5>
                                        <div id="previewInfo" class="text-muted">
                                            {{ lang('Select month and year to see preview') }}
                                        </div>
                                        <div id="previewContent" class="mt-2" style="display: none;">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <strong>{{ lang('Selected Period') }}:</strong>
                                                    <span id="previewPeriod"></span>
                                                </div>
                                                <div class="col-md-4">
                                                    <strong>{{ lang('Total Tickets') }}:</strong>
                                                    <span id="previewCount">0</span>
                                                </div>
                                                <div class="col-md-4">
                                                    <strong>{{ lang('File Size') }}:</strong>
                                                    <span id="previewSize">~0 KB</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="btn-list">
                                    <button type="submit" class="btn btn-primary" id="exportButton">
                                        <i class="fa fa-download me-2"></i>{{ lang('Export Tickets') }}
                                    </button>
                                    
                                    <button type="button" class="btn btn-outline-secondary" id="previewButton">
                                        <i class="fa fa-eye me-2"></i>{{ lang('Preview Data') }}
                                    </button>
                                    
                                    <button type="reset" class="btn btn-light">
                                        <i class="fa fa-refresh me-2"></i>{{ lang('Reset Form') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Quick Export Sidebar -->
        <div class="col-xl-4 col-lg-4 col-md-12">
            <!-- Quick Export Card -->
            <div class="card">
                <div class="card-header border-bottom-0">
                    <h3 class="card-title">
                        <i class="fa fa-bolt me-2"></i>{{ lang('Quick Export') }}
                    </h3>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <a href="{{ route('admin.reports.export.monthly', ['month' => $currentMonth, 'year' => $currentYear, 'format' => 'xlsx']) }}" 
                           class="list-group-item list-group-item-action">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ lang('Current Month') }}</h6>
                                    <small class="text-muted">{{ date('F Y') }}</small>
                                </div>
                                <div class="flex-shrink-0">
                                    <span class="badge bg-primary">{{ $currentMonthStats['total_tickets'] ?? 0 }} {{ lang('tickets') }}</span>
                                </div>
                            </div>
                        </a>
                        
                        <a href="{{ route('admin.reports.export.monthly', ['month' => $prevMonth, 'year' => $prevMonthYear, 'format' => 'xlsx']) }}" 
                           class="list-group-item list-group-item-action">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ lang('Previous Month') }}</h6>
                                    <small class="text-muted">{{ \Carbon\Carbon::create()->month($prevMonth)->year($prevMonthYear)->format('F Y') }}</small>
                                </div>
                                <div class="flex-shrink-0">
                                    <span class="badge bg-info">{{ $prevMonthStats['total_tickets'] ?? 0 }} {{ lang('tickets') }}</span>
                                </div>
                            </div>
                        </a>
                        
                        <a href="{{ route('admin.reports.export.all') }}" class="list-group-item list-group-item-action">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ lang('All Tickets') }}</h6>
                                    <small class="text-muted">{{ lang('Complete history') }}</small>
                                </div>
                                <div class="flex-shrink-0">
                                    <span class="badge bg-success">{{ $totalTickets }} {{ lang('tickets') }}</span>
                                </div>
                            </div>
                        </a>
                        
                        <a href="{{ route('admin.reports.export.current-year') }}" class="list-group-item list-group-item-action">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ lang('Current Year') }}</h6>
                                    <small class="text-muted">{{ date('Y') }}</small>
                                </div>
                                <div class="flex-shrink-0">
                                    <span class="badge bg-warning">{{ $currentYearTickets }} {{ lang('tickets') }}</span>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Export Statistics -->
            <div class="card mt-4">
                <div class="card-header border-bottom-0">
                    <h3 class="card-title">
                        <i class="fa fa-chart-bar me-2"></i>{{ lang('Export Statistics') }}
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="mb-3">
                                <span class="fs-20 font-weight-bold text-primary">{{ $totalExports }}</span>
                                <div class="text-muted small">{{ lang('Total Exports') }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-3">
                                <span class="fs-20 font-weight-bold text-success">{{ $lastExportDate ? $lastExportDate->format('M d') : 'N/A' }}</span>
                                <div class="text-muted small">{{ lang('Last Export') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Help Section -->
            <div class="card mt-4">
                <div class="card-header border-bottom-0">
                    <h3 class="card-title">
                        <i class="fa fa-question-circle me-2"></i>{{ lang('Export Help') }}
                    </h3>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-borderless">
                        <div class="list-group-item px-0">
                            <div class="d-flex align-items-start">
                                <div class="me-3">
                                    <span class="avatar avatar-sm bg-primary-transparent rounded-circle">1</span>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ lang('Select Period') }}</h6>
                                    <small class="text-muted">{{ lang('Choose the month and year for export') }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="list-group-item px-0">
                            <div class="d-flex align-items-start">
                                <div class="me-3">
                                    <span class="avatar avatar-sm bg-success-transparent rounded-circle">2</span>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ lang('Choose Format') }}</h6>
                                    <small class="text-muted">{{ lang('Excel for analysis, CSV for import') }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="list-group-item px-0">
                            <div class="d-flex align-items-start">
                                <div class="me-3">
                                    <span class="avatar avatar-sm bg-info-transparent rounded-circle">3</span>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ lang('Apply Filters') }}</h6>
                                    <small class="text-muted">{{ lang('Optional filters for specific data') }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="list-group-item px-0">
                            <div class="d-flex align-items-start">
                                <div class="me-3">
                                    <span class="avatar avatar-sm bg-warning-transparent rounded-circle">4</span>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ lang('Download') }}</h6>
                                    <small class="text-muted">{{ lang('File will download automatically') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--End Export Form-->

    <!-- Recent Exports Table -->
    <div class="row mt-4">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header border-bottom-0">
                    <h3 class="card-title">
                        <i class="fa fa-history me-2"></i>{{ lang('Recent Exports') }}
                    </h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-vcenter text-nowrap">
                            <thead>
                                <tr>
                                    <th>{{ lang('Export Date') }}</th>
                                    <th>{{ lang('Period') }}</th>
                                    <th>{{ lang('Format') }}</th>
                                    <th>{{ lang('Ticket Count') }}</th>
                                    <th>{{ lang('File Size') }}</th>
                                    <th>{{ lang('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentExports as $export)
                                    <tr>
                                        <td>{{ $export->created_at->format('M d, Y H:i') }}</td>
                                        <td>{{ $export->month_name }} {{ $export->year }}</td>
                                        <td>
                                            <span class="badge bg-{{ $export->format == 'xlsx' ? 'success' : 'info' }}">
                                                {{ strtoupper($export->format) }}
                                            </span>
                                        </td>
                                        <td>{{ $export->ticket_count }}</td>
                                        <td>{{ $export->file_size }}</td>
                                        <td>
                                            <div class="btn-list">
                                                <a href="#" class="btn btn-sm btn-outline-primary">
                                                    <i class="fa fa-download"></i>
                                                </a>
                                                <a href="#" class="btn btn-sm btn-outline-info">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            <i class="fa fa-inbox fa-2x mb-2"></i>
                                            <br>{{ lang('No export history found') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--End Recent Exports Table-->
@endsection

@section('scripts')
    <!-- INTERNAL Select2 js -->
    <script src="{{ asset('assets/plugins/select2/select2.full.min.js') }}?v=<?php echo time(); ?>"></script>

    <script type="text/javascript">
        "use strict";

        $(document).ready(function() {
            // Initialize Select2
            $('.form-select').select2({
                minimumResultsForSearch: Infinity,
                width: '100%'
            });

            // Preview functionality
            function updatePreview() {
                const month = $('select[name="month"]').val();
                const year = $('select[name="year"]').val();
                
                if (month && year) {
                    const monthName = $('select[name="month"] option:selected').text();
                    $('#previewPeriod').text(monthName + ' ' + year);
                    $('#previewContent').show();
                    $('#previewInfo').hide();
                    
                    // Simulate ticket count (in real app, this would be an AJAX call)
                    const simulatedCount = Math.floor(Math.random() * 100) + 50;
                    $('#previewCount').text(simulatedCount);
                    $('#previewSize').text('~' + Math.round(simulatedCount * 0.5) + ' KB');
                } else {
                    $('#previewContent').hide();
                    $('#previewInfo').show();
                }
            }

            // Event listeners for preview
            $('select[name="month"], select[name="year"]').on('change', function() {
                updatePreview();
            });

            // Preview button
            $('#previewButton').on('click', function() {
                const month = $('select[name="month"]').val();
                const year = $('select[name="year"]').val();
                
                if (!month || !year) {
                    alert('{{ lang("Please select both month and year") }}');
                    return;
                }
                
                // In real application, this would open a modal with actual data preview
                alert('{{ lang("Preview feature would show actual ticket data here") }}');
            });

            // Form submission handling
            $('#exportForm').on('submit', function(e) {
                const month = $('select[name="month"]').val();
                const year = $('select[name="year"]').val();
                const format = $('input[name="format"]:checked').val();
                
                if (!month || !year) {
                    e.preventDefault();
                    alert('{{ lang("Please select both month and year") }}');
                    return false;
                }
                
                // Show loading state
                $('#exportButton').prop('disabled', true).html(
                    '<i class="fa fa-spinner fa-spin me-2"></i>{{ lang("Exporting...") }}'
                );
                
                // File will download automatically after form submission
            });

            // Quick export links
            $('.quick-export-link').on('click', function(e) {
                e.preventDefault();
                const url = $(this).attr('href');
                
                // Show confirmation for large exports
                const ticketCount = $(this).find('.badge').text();
                if (parseInt(ticketCount) > 1000) {
                    if (!confirm('{{ lang("This export contains") }} ' + ticketCount + ' {{ lang("tickets. It might take a while. Continue?") }}')) {
                        return;
                    }
                }
                
                window.location.href = url;
            });

            // Auto-update preview on page load if values are preselected
            updatePreview();

            // Column selection toggle
            $('input[name="columns[]"]').on('change', function() {
                const checkedColumns = $('input[name="columns[]"]:checked').length;
                if (checkedColumns === 0) {
                    alert('{{ lang("Please select at least one column to export") }}');
                    $(this).prop('checked', true);
                }
            });

            // Keyboard shortcuts
            $(document).on('keydown', function(e) {
                // Ctrl+E for export
                if (e.ctrlKey && e.key === 'e') {
                    e.preventDefault();
                    $('#exportButton').click();
                }
                
                // Ctrl+P for preview
                if (e.ctrlKey && e.key === 'p') {
                    e.preventDefault();
                    $('#previewButton').click();
                }
            });

            // Initialize tooltips
            $('[data-bs-toggle="tooltip"]').tooltip();
        });
    </script>
@endsection