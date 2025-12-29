<?php
/**
 * Admissions Manager Page Template
 * @package AI_Admission_Counselor
 */
if (!defined('ABSPATH')) exit;

function aiac_admissions_page_new() {
    $currency = get_option('aiac_currency', 'PKR');
    ?>
    <div id="aiac-admissions-root" class="aiac-wrap">
        <header class="aiac-header">
            <div class="aiac-header-title">
                <h1>Admissions Manager</h1>
                <p>Manage student admissions and fee tracking</p>
            </div>
            <div class="aiac-header-actions">
                <button class="aiac-btn aiac-btn-primary" id="aiac-add-admission-btn">+ New Admission</button>
                <button class="aiac-btn aiac-btn-secondary" id="aiac-export-admissions">Export</button>
            </div>
        </header>

        <!-- Stats Summary -->
        <div class="aiac-stats-grid aiac-stats-sm">
            <div class="aiac-card">
                <h4>Total Admissions</h4>
                <div class="aiac-stat-value" id="stat-admissions-count">0</div>
            </div>
            <div class="aiac-card">
                <h4>Total Fee</h4>
                <div class="aiac-stat-value" id="stat-total-fee"><?php echo esc_html($currency); ?> 0</div>
            </div>
            <div class="aiac-card">
                <h4>Collected</h4>
                <div class="aiac-stat-value aiac-success" id="stat-collected"><?php echo esc_html($currency); ?> 0</div>
            </div>
            <div class="aiac-card">
                <h4>Pending</h4>
                <div class="aiac-stat-value aiac-danger" id="stat-pending"><?php echo esc_html($currency); ?> 0</div>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="aiac-card aiac-filter-bar">
            <div class="aiac-filter-row">
                <input type="text" id="aiac-admission-search" placeholder="Search student..." class="aiac-search-input">
                <select id="aiac-admission-status-filter" class="aiac-select">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="partial">Partial Paid</option>
                    <option value="paid">Fully Paid</option>
                </select>
                <button class="aiac-btn aiac-btn-secondary" id="aiac-admission-filter-btn">Filter</button>
            </div>
        </div>

        <!-- Admissions Table -->
        <div class="aiac-card aiac-table-card">
            <table class="aiac-table" id="aiac-admissions-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Student</th>
                        <th>Phone</th>
                        <th>Course</th>
                        <th>Total Fee</th>
                        <th>Paid</th>
                        <th>Balance</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="aiac-admissions-list">
                    <tr><td colspan="10" class="aiac-loading">Loading...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add/Edit Admission Modal -->
    <div id="aiac-admission-modal" class="aiac-modal" style="display:none;">
        <div class="aiac-modal-content aiac-modal-lg">
            <div class="aiac-modal-header">
                <h2 id="aiac-admission-modal-title">New Admission</h2>
                <button class="aiac-modal-close">&times;</button>
            </div>
            <form id="aiac-admission-form">
                <input type="hidden" id="aiac-admission-id" name="admission_id" value="">
                
                <div class="aiac-form-group">
                    <label for="aiac-admission-lead">Select Lead *</label>
                    <select id="aiac-admission-lead" name="lead_id" required>
                        <option value="">-- Select Lead --</option>
                    </select>
                </div>
                
                <div class="aiac-form-group">
                    <label for="aiac-admission-course">Select Course *</label>
                    <select id="aiac-admission-course" name="course_id" required>
                        <option value="">-- Select Course --</option>
                        <?php
                        // Courses will be loaded via AJAX, but we can also show default options
                        $db = new AIAC_DB();
                        $courses = $db->fetch_all_courses(true);
                        if (!empty($courses)) {
                            foreach ($courses as $course) {
                                echo '<option value="' . esc_attr($course['id']) . '">' . esc_html($course['course_name']) . ' - ' . esc_html($currency) . ' ' . number_format($course['fee']) . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
                
                <div class="aiac-form-row">
                    <div class="aiac-form-group">
                        <label for="aiac-admission-fee">Total Fee (<?php echo esc_html($currency); ?>) *</label>
                        <input type="number" id="aiac-admission-fee" name="total_fee" required min="0" step="0.01">
                    </div>
                    <div class="aiac-form-group">
                        <label for="aiac-admission-paid">Paid Amount (<?php echo esc_html($currency); ?>)</label>
                        <input type="number" id="aiac-admission-paid" name="paid_amount" value="0" min="0" step="0.01">
                    </div>
                </div>
                
                <div class="aiac-form-row">
                    <div class="aiac-form-group">
                        <label for="aiac-admission-due">Due Date</label>
                        <input type="date" id="aiac-admission-due" name="due_date">
                    </div>
                    <div class="aiac-form-group">
                        <label for="aiac-admission-status">Status</label>
                        <select id="aiac-admission-status" name="admission_status">
                            <option value="pending">Pending</option>
                            <option value="partial">Partial Paid</option>
                            <option value="paid">Fully Paid</option>
                        </select>
                    </div>
                </div>
                
                <div class="aiac-form-actions">
                    <button type="submit" class="aiac-btn aiac-btn-primary" id="aiac-save-admission">Save Admission</button>
                    <button type="button" class="aiac-btn aiac-btn-secondary aiac-modal-close">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation -->
    <div id="aiac-delete-admission-modal" class="aiac-modal" style="display:none;">
        <div class="aiac-modal-content aiac-modal-sm">
            <h3>Confirm Delete</h3>
            <p>Are you sure you want to delete this admission record?</p>
            <input type="hidden" id="aiac-delete-admission-id">
            <div class="aiac-form-actions">
                <button class="aiac-btn aiac-btn-danger" id="aiac-confirm-delete-admission">Delete</button>
                <button class="aiac-btn aiac-btn-secondary aiac-modal-close">Cancel</button>
            </div>
        </div>
    </div>
    <?php
}
