<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Approval Result</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .header {
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header-approved {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        }
        .header-rejected {
            background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .content {
            padding: 30px;
        }
        .status-badge {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 20px;
        }
        .status-approved {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .status-rejected {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .ticket-info {
            background: #f8f9fa;
            border-radius: 5px;
            padding: 20px;
            margin: 20px 0;
        }
        .info-item {
            margin-bottom: 10px;
            display: flex;
        }
        .info-label {
            font-weight: 600;
            min-width: 120px;
            color: #555;
        }
        .info-value {
            color: #333;
        }
        .approver-info {
            background: #e7f3ff;
            border: 1px solid #b3d9ff;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
        }
        .action-required {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
            text-align: center;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn:hover {
            background: #5a6fd8;
            transform: translateY(-2px);
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
        }
        .icon {
            font-size: 48px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        @if($approval_status == 'approved')
        <div class="header header-approved">
            <div class="icon">✅</div>
            <h1>Ticket Approved</h1>
            <p>The ticket has been approved successfully</p>
        </div>
        @else
        <div class="header header-rejected">
            <div class="icon">❌</div>
            <h1>Ticket Rejected</h1>
            <p>The ticket has been rejected</p>
        </div>
        @endif
        
        <div class="content">
            <div style="text-align: center;">
                <span class="status-badge status-{{ $approval_status }}">
                    {{ ucfirst($approval_status) }}
                </span>
            </div>

            <div class="ticket-info">
                <div class="info-item">
                    <span class="info-label">Ticket ID:</span>
                    <span class="info-value">#{{ $ticket_id }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Subject:</span>
                    <span class="info-value">{{ $ticket_subject }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Approved By:</span>
                    <span class="info-value">{{ $approver_email }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Approval Time:</span>
                    <span class="info-value">{{ $approved_at->format('F j, Y \a\t g:i A') }}</span>
                </div>
            </div>

            <div class="approver-info">
                <strong>👤 Approval Decision:</strong>
                <p style="margin: 10px 0 0 0;">
                    @if($approval_status == 'approved')
                    The ticket has been <strong>approved</strong> by <strong>{{ $approver_email }}</strong> 
                    and is now ready for further processing.
                    @else
                    The ticket has been <strong>rejected</strong> by <strong>{{ $approver_email }}</strong> 
                    and will not proceed further.
                    @endif
                </p>
            </div>

            @if($approval_status == 'rejected')
            <div class="action-required">
                <strong>⚠️ Action Required:</strong>
                <p style="margin: 10px 0;">
                    This ticket has been rejected. Please review the ticket and take appropriate action.
                    You may need to contact the requester for more information or create a new ticket.
                </p>
            </div>
            @endif

            <div style="text-align: center; margin-top: 30px;">
                <a href="{{ $ticket_url }}" class="btn">
                    📋 View Ticket Details
                </a>
            </div>

            <div style="margin-top: 30px; padding: 15px; background: #f8f9fa; border-radius: 5px;">
                <p style="margin: 0; color: #666; font-size: 14px; text-align: center;">
                    <strong>Next Steps:</strong><br>
                    @if($approval_status == 'approved')
                    The ticket will now proceed to the next stage in the workflow.
                    @else
                    The ticket has been closed. Please follow up with the requester if needed.
                    @endif
                </p>
            </div>
        </div>
        
        <div class="footer">
            <p>This is an automated notification. Please do not reply to this email.</p>
            <p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>