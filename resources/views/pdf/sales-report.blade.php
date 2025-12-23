<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sales Report - {{ $generatedDate }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
            padding: 30px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #ea580c;
            padding-bottom: 15px;
        }
        .header h1 {
            color: #ea580c;
            font-size: 28px;
            margin-bottom: 5px;
        }
        .header p {
            color: #666;
            font-size: 11px;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 25px;
        }
        .info-row {
            display: table-row;
        }
        .info-cell {
            display: table-cell;
            width: 33.33%;
            padding: 12px;
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            vertical-align: top;
        }
        .info-cell h3 {
            font-size: 10px;
            color: #6b7280;
            text-transform: uppercase;
            margin-bottom: 5px;
            font-weight: 600;
        }
        .info-cell .value {
            font-size: 18px;
            font-weight: bold;
            color: #1f2937;
        }
        .info-cell.green .value {
            color: #059669;
        }
        .info-cell.blue .value {
            color: #2563eb;
        }
        .info-cell.purple .value {
            color: #7c3aed;
        }
        .info-cell.orange .value {
            color: #ea580c;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #1f2937;
            margin: 25px 0 15px 0;
            padding-bottom: 8px;
            border-bottom: 2px solid #ea580c;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th {
            background-color: #f3f4f6;
            color: #374151;
            font-weight: 600;
            padding: 10px;
            text-align: left;
            border-bottom: 2px solid #d1d5db;
            font-size: 11px;
            text-transform: uppercase;
        }
        table td {
            padding: 10px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 11px;
        }
        table tr:hover {
            background-color: #f9fafb;
        }
        table tr:last-child td {
            border-bottom: none;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .font-bold {
            font-weight: bold;
        }
        .text-green {
            color: #059669;
        }
        .footer {
            margin-top: 40px;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 10px;
            color: #6b7280;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>KantinCanteen Sales Report</h1>
        <p>Generated on {{ $generatedDate }}</p>
    </div>

    <!-- Revenue Statistics -->
    <div class="section-title">Revenue Overview</div>
    <div class="info-grid">
        <div class="info-row">
            <div class="info-cell green">
                <h3>Total Revenue</h3>
                <div class="value">RM {{ number_format($revenueStats['total_revenue'], 2) }}</div>
            </div>
            <div class="info-cell blue">
                <h3>Average Order Value</h3>
                <div class="value">RM {{ number_format($revenueStats['average_order_value'], 2) }}</div>
            </div>
            <div class="info-cell purple">
                <h3>Today's Revenue</h3>
                <div class="value">RM {{ number_format($revenueStats['today_revenue'], 2) }}</div>
            </div>
        </div>
        <div class="info-row">
            <div class="info-cell orange">
                <h3>This Week</h3>
                <div class="value">RM {{ number_format($revenueStats['this_week_revenue'], 2) }}</div>
            </div>
            <div class="info-cell green">
                <h3>This Month</h3>
                <div class="value">RM {{ number_format($revenueStats['this_month_revenue'], 2) }}</div>
            </div>
            <div class="info-cell blue">
                <h3>Total Orders</h3>
                <div class="value">{{ $orderStats['total_orders'] }}</div>
            </div>
        </div>
    </div>

    <!-- Order Statistics -->
    <div class="section-title">Order Statistics</div>
    <div class="info-grid">
        <div class="info-row">
            <div class="info-cell green">
                <h3>Completed Orders</h3>
                <div class="value">{{ $orderStats['completed_orders'] }}</div>
            </div>
            <div class="info-cell blue">
                <h3>Pending Orders</h3>
                <div class="value">{{ $orderStats['pending_orders'] }}</div>
            </div>
            <div class="info-cell orange">
                <h3>Cancelled Orders</h3>
                <div class="value">{{ $orderStats['cancelled_orders'] }}</div>
            </div>
        </div>
    </div>

    <!-- Revenue by Category -->
    @if(count($revenueByCategory) > 0)
    <div class="section-title">Revenue by Category</div>
    <table>
        <thead>
            <tr>
                <th>Category</th>
                <th class="text-right">Revenue (RM)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($revenueByCategory as $category)
            <tr>
                <td class="font-bold">{{ $category['category'] }}</td>
                <td class="text-right text-green font-bold">{{ number_format($category['revenue'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- Top Selling Items -->
    @if(count($topSellingItems) > 0)
    <div class="section-title">Top Selling Items</div>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Product Name</th>
                <th>Category</th>
                <th class="text-center">Quantity Sold</th>
                <th class="text-right">Total Revenue (RM)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($topSellingItems as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td class="font-bold">{{ $item['name'] }}</td>
                <td>{{ $item['category'] }}</td>
                <td class="text-center font-bold">{{ $item['total_quantity'] }}</td>
                <td class="text-right text-green font-bold">{{ number_format($item['total_revenue'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>&copy; {{ date('Y') }} KantinCanteen. All rights reserved.</p>
        <p>This report was automatically generated and contains confidential business information.</p>
    </div>
</body>
</html>
