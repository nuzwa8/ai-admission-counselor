/** Part 6 — Professional Dashboard Template */
function aiac_dashboard_page() {
    ?>
    <div id="aiac-dashboard-root" class="aiac-wrap">
        <header class="aiac-header">
            <div class="aiac-header-title">
                <h1>AI Admission Dashboard</h1>
                <p>Overview of leads, admissions, and financial performance.</p>
            </div>
            <div class="aiac-header-actions">
                <button class="aiac-btn aiac-btn-secondary" id="aiac-import-demo">Import Demo Data</button>
                <button class="aiac-btn aiac-btn-primary" id="aiac-export-excel">Export to Excel</button>
            </div>
        </header>

        <div class="aiac-stats-grid">
            <div class="aiac-card">
                <h3>Total Leads</h3>
                <div class="aiac-stat-value" id="stat-total-leads">0</div>
                <span class="aiac-stat-label">Initial Inquiries</span>
            </div>
            <div class="aiac-card">
                <h3>Admissions</h3>
                <div class="aiac-stat-value" id="stat-total-admissions">0</div>
                <span class="aiac-stat-label">Confirmed Students</span>
            </div>
            <div class="aiac-card">
                <h3>Total Revenue</h3>
                <div class="aiac-stat-value" id="stat-total-revenue">$0</div>
                <span class="aiac-stat-label">Collected Fees</span>
            </div>
            <div class="aiac-card">
                <h3>Pending Balance</h3>
                <div class="aiac-stat-value" id="stat-pending-balance" style="color: #e74c3c;">$0</div>
                <span class="aiac-stat-label">Next Due: <strong id="next-due-date">N/A</strong></span>
            </div>
        </div>

        <div class="aiac-content-section">
            <div class="aiac-card aiac-table-card">
                <div class="card-header">
                    <h2>Recent Admissions</h2>
                    <button class="aiac-btn-text" onclick="window.print()">Print Report</button>
                </div>
                <table class="aiac-table">
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Course</th>
                            <th>Status</th>
                            <th>Balance</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="aiac-recent-admissions-list">
                        <tr>
                            <td colspan="5" style="text-align:center;">Loading dynamic data...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php
}
// ✅ Syntax verified block end
