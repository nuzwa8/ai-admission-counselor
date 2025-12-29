<?php
/**
 * Leads Manager Page Template
 * @package AI_Admission_Counselor
 */
if (!defined('ABSPATH')) exit;

function aiac_leads_page_new() {
    $currency = get_option('aiac_currency', 'PKR');
    ?>
    <div id="aiac-leads-root" class="aiac-wrap">
        <header class="aiac-header">
            <div class="aiac-header-title">
                <h1>Leads Manager</h1>
                <p>Track and manage student inquiries</p>
            </div>
            <div class="aiac-header-actions">
                <button class="aiac-btn aiac-btn-primary" id="aiac-add-lead-btn">+ Add New Lead</button>
                <button class="aiac-btn aiac-btn-secondary" id="aiac-export-leads">Export</button>
            </div>
        </header>

        <!-- Search & Filter Bar -->
        <div class="aiac-card aiac-filter-bar">
            <div class="aiac-filter-row">
                <input type="text" id="aiac-lead-search" placeholder="Search by name or phone..." class="aiac-search-input">
                <select id="aiac-lead-status-filter" class="aiac-select">
                    <option value="">All Status</option>
                    <option value="new">New</option>
                    <option value="contacted">Contacted</option>
                    <option value="interested">Interested</option>
                    <option value="converted">Converted</option>
                    <option value="lost">Lost</option>
                </select>
                <button class="aiac-btn aiac-btn-secondary" id="aiac-filter-btn">Filter</button>
            </div>
        </div>

        <!-- Leads Table -->
        <div class="aiac-card aiac-table-card">
            <table class="aiac-table" id="aiac-leads-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Student Name</th>
                        <th>Phone</th>
                        <th>Course</th>
                        <th>Language</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="aiac-leads-list">
                    <tr><td colspan="7" class="aiac-loading">Loading...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add/Edit Lead Modal -->
    <div id="aiac-lead-modal" class="aiac-modal" style="display:none;">
        <div class="aiac-modal-content">
            <div class="aiac-modal-header">
                <h2 id="aiac-modal-title">Add New Lead</h2>
                <button class="aiac-modal-close">&times;</button>
            </div>
            <form id="aiac-lead-form">
                <input type="hidden" id="aiac-lead-id" name="lead_id" value="">
                
                <div class="aiac-form-group">
                    <label for="aiac-lead-name">Student Name *</label>
                    <input type="text" id="aiac-lead-name" name="student_name" required>
                </div>
                
                <div class="aiac-form-row">
                    <div class="aiac-form-group">
                        <label for="aiac-lead-phone">Phone Number *</label>
                        <input type="text" id="aiac-lead-phone" name="phone_number" required>
                    </div>
                    <div class="aiac-form-group">
                        <label for="aiac-lead-course">Interested Course</label>
                        <input type="text" id="aiac-lead-course" name="course_id" placeholder="e.g., Web Development">
                    </div>
                </div>
                
                <div class="aiac-form-row">
                    <div class="aiac-form-group">
                        <label for="aiac-lead-lang">Language</label>
                        <select id="aiac-lead-lang" name="language_detected">
                            <option value="Urdu">Urdu</option>
                            <option value="English">English</option>
                            <option value="Arabic">Arabic</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="aiac-form-group">
                        <label for="aiac-lead-status">Status</label>
                        <select id="aiac-lead-status" name="status">
                            <option value="new">New</option>
                            <option value="contacted">Contacted</option>
                            <option value="interested">Interested</option>
                            <option value="converted">Converted</option>
                            <option value="lost">Lost</option>
                        </select>
                    </div>
                </div>
                
                <div class="aiac-form-actions">
                    <button type="submit" class="aiac-btn aiac-btn-primary" id="aiac-save-lead">Save Lead</button>
                    <button type="button" class="aiac-btn aiac-btn-secondary aiac-modal-close">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="aiac-delete-modal" class="aiac-modal" style="display:none;">
        <div class="aiac-modal-content aiac-modal-sm">
            <h3>Confirm Delete</h3>
            <p>Are you sure you want to delete this lead?</p>
            <input type="hidden" id="aiac-delete-lead-id">
            <div class="aiac-form-actions">
                <button class="aiac-btn aiac-btn-danger" id="aiac-confirm-delete">Delete</button>
                <button class="aiac-btn aiac-btn-secondary aiac-modal-close">Cancel</button>
            </div>
        </div>
    </div>
    <?php
}
