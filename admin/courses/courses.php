<?php
/**
 * Courses Manager Page Template
 * @package AI_Admission_Counselor
 */
if (!defined('ABSPATH')) exit;

function aiac_courses_page_new() {
    $currency = get_option('aiac_currency', 'PKR');
    ?>
    <div id="aiac-courses-root" class="aiac-wrap">
        <header class="aiac-header">
            <div class="aiac-header-title">
                <h1>Courses Manager</h1>
                <p>Manage available courses and their fees</p>
            </div>
            <div class="aiac-header-actions">
                <button class="aiac-btn aiac-btn-primary" id="aiac-add-course-btn">+ Add New Course</button>
            </div>
        </header>

        <!-- Stats Summary -->
        <div class="aiac-stats-grid aiac-stats-sm">
            <div class="aiac-card">
                <h4>Total Courses</h4>
                <div class="aiac-stat-value" id="stat-courses-count">0</div>
            </div>
            <div class="aiac-card">
                <h4>Active Courses</h4>
                <div class="aiac-stat-value aiac-success" id="stat-active-courses">0</div>
            </div>
        </div>

        <!-- Courses Table -->
        <div class="aiac-card aiac-table-card">
            <table class="aiac-table" id="aiac-courses-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Course Name</th>
                        <th>Code</th>
                        <th>Duration</th>
                        <th>Fee</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="aiac-courses-list">
                    <tr><td colspan="7" class="aiac-loading">Loading...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add/Edit Course Modal -->
    <div id="aiac-course-modal" class="aiac-modal" style="display:none;">
        <div class="aiac-modal-content">
            <div class="aiac-modal-header">
                <h2 id="aiac-course-modal-title">Add New Course</h2>
                <button class="aiac-modal-close">&times;</button>
            </div>
            <form id="aiac-course-form">
                <input type="hidden" id="aiac-course-id" name="course_id" value="">
                
                <div class="aiac-form-group">
                    <label for="aiac-course-name">Course Name *</label>
                    <input type="text" id="aiac-course-name" name="course_name" required>
                </div>
                
                <div class="aiac-form-row">
                    <div class="aiac-form-group">
                        <label for="aiac-course-code">Course Code</label>
                        <input type="text" id="aiac-course-code" name="course_code" placeholder="e.g., WEB-101">
                    </div>
                    <div class="aiac-form-group">
                        <label for="aiac-course-duration">Duration</label>
                        <input type="text" id="aiac-course-duration" name="duration" placeholder="e.g., 3 months">
                    </div>
                </div>
                
                <div class="aiac-form-group">
                    <label for="aiac-course-fee">Fee (<?php echo esc_html($currency); ?>)</label>
                    <input type="number" id="aiac-course-fee" name="fee" value="0" min="0" step="0.01">
                </div>
                
                <div class="aiac-form-group">
                    <label for="aiac-course-description">Description</label>
                    <textarea id="aiac-course-description" name="description" rows="3"></textarea>
                </div>
                
                <div class="aiac-form-actions">
                    <button type="submit" class="aiac-btn aiac-btn-primary" id="aiac-save-course">Save Course</button>
                    <button type="button" class="aiac-btn aiac-btn-secondary aiac-modal-close">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation -->
    <div id="aiac-delete-course-modal" class="aiac-modal" style="display:none;">
        <div class="aiac-modal-content aiac-modal-sm">
            <h3>Confirm Delete</h3>
            <p>Are you sure you want to deactivate this course?</p>
            <input type="hidden" id="aiac-delete-course-id">
            <div class="aiac-form-actions">
                <button class="aiac-btn aiac-btn-danger" id="aiac-confirm-delete-course">Deactivate</button>
                <button class="aiac-btn aiac-btn-secondary aiac-modal-close">Cancel</button>
            </div>
        </div>
    </div>
    <?php
}
