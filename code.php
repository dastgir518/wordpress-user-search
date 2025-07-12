// Shortcode to display filters and results
function course_filter_search_shortcode() {
    ob_start(); ?>
    <div id="course-filter-container">
        <div class="filter-box">
            <h3>Search by speaker</h3>
            <div class="form-group">
                <label for="speaker-search">Search Speaker:</label>
                <input type="text" id="speaker-search" placeholder="Start typing speaker name or click to see all...">
                <div id="speaker-results" class="speaker-results"></div>
                <input type="hidden" id="selected-speaker-id">
            </div>
            <div id="selected-speaker" class="selected-speaker" style="display:none;">
                <strong>Selected Speaker:</strong> <span id="speaker-name"></span>
            </div>
        </div>
        
        <div id="results-container">
            <div id="author-info"></div>
            <div id="course-results" class="course-grid"></div>
        </div>
    </div>

    <style>
        /* Speaker Search Styles */
        .speaker-results {
            display: none;
            border: 1px solid #ddd;
            max-height: 300px;
            overflow-y: auto;
            margin-top: 5px;
            border-radius: 4px;
            background: white;
            z-index: 100;
            position: absolute;
            width: calc(100% - 2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .speaker-item {
            padding: 12px 15px;
            cursor: pointer;
            border-bottom: 1px solid #eee;
            transition: background 0.3s ease;
            font-size: 16px;
            display: flex;
            align-items: center;
        }
        .speaker-item:hover {
            background-color: #f0f7ff;
            color: #1a5db0;
        }
        .speaker-item:before {
            content: "•";
            color: #1a5db0;
            margin-right: 10px;
            font-size: 24px;
            line-height: 1;
        }
        .selected-speaker {
            background: #e8f4ff;
            padding: 15px;
            margin-top: 15px;
            border: 1px solid #c2e0ff;
            border-radius: 8px;
            font-size: 18px;
            color: #0d4a9e;
        }
        
        /* Course Grid Layout */
        .course-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
            margin-top: 30px;
        }
        .course-card {
            background: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid #eaeaea;
        }
        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }
        .course-image {
            height: 180px;
            overflow: hidden;
        }
        .course-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        .course-card:hover .course-image img {
            transform: scale(1.05);
        }
        .course-content {
            padding: 40px;
        }
        .course-content h3 {
            margin-top: 0;
            margin-bottom: 15px;
            font-size: 20px;
            color: #1a365d;
        }
        .course-content p {
            color: #4a5568;
            line-height: 1.6;
            margin-bottom: 20px;
            overflow-wrap: break-word;
            word-wrap: break-word;
            word-break: break-word;
            overflow: hidden;
        }
        .course-button {
            display: inline-block;
            background: #1a5db0;
            color: white !important;
            padding: 12px 25px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            transition: background 0.3s ease, transform 0.2s ease;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }
        .course-button:hover {
            background: #0d4a9e;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(13, 74, 158, 0.25);
        }
        
        /* Author Info Styling */
        .author-box {
            background: linear-gradient(135deg, #f0f7ff 0%, #e3eeff 100%);
            padding: 25px;
            border-radius: 10px;
            border: 1px solid #c2e0ff;
            margin-bottom: 30px;
        }
        .author-box h3 {
            color: #1a365d;
            margin-top: 0;
            font-size: 24px;
            border-bottom: 2px solid #a0c7ff;
            padding-bottom: 10px;
        }
        .author-box p {
            color: #2d3748;
            line-height: 1.7;
            margin-bottom: 15px;
        }
        
        /* Responsive Adjustments */
        @media (max-width: 992px) {
            .course-grid {
                grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            }
        }
        @media (max-width: 768px) {
            .course-grid {
                grid-template-columns: 1fr;
            }
            .course-content {
                padding: 25px;
            }
        }
    </style>

    <script>
    jQuery(document).ready(function($) {
        var searchTimer;
        var allSpeakers = [];
        var initialLoadDone = false;
        
        // Function to load all speakers
        function loadAllSpeakers() {
            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                data: {
                    action: 'get_all_speakers',
                    nonce: '<?php echo wp_create_nonce('course_filter_nonce'); ?>'
                },
                success: function(response) {
                    if(response.success) {
                        allSpeakers = response.data;
                        displaySpeakers(allSpeakers);
                    }
                }
            });
        }
        
        // Function to display speakers in results
        function displaySpeakers(speakers) {
            if (speakers.length > 0) {
                var resultsHtml = '';
                $.each(speakers, function(index, speaker) {
                    resultsHtml += '<div class="speaker-item" data-id="'+speaker.id+'">'+speaker.name+'</div>';
                });
                $('#speaker-results').html(resultsHtml).show();
            } else {
                $('#speaker-results').html('<div class="speaker-item">No speakers found</div>').show();
            }
        }
        
        // Focus event to show all speakers
        $('#speaker-search').on('focus', function() {
            if (!initialLoadDone) {
                $('#speaker-results').html('<div class="speaker-item">Loading all speakers...</div>').show();
                loadAllSpeakers();
                initialLoadDone = true;
            } else if (allSpeakers.length > 0) {
                displaySpeakers(allSpeakers);
            }
        });
        
        // Click outside to close results
        $(document).on('click', function(e) {
            if (!$(e.target).closest('#speaker-search, #speaker-results').length) {
                $('#speaker-results').hide();
            }
        });
        
        // Speaker search functionality
        $('#speaker-search').on('input', function() {
            clearTimeout(searchTimer);
            var query = $(this).val().trim();
            
            if (query.length === 0 && allSpeakers.length > 0) {
                displaySpeakers(allSpeakers);
                return;
            }
            
            searchTimer = setTimeout(function() {
                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    type: 'POST',
                    data: {
                        action: 'search_speakers',
                        nonce: '<?php echo wp_create_nonce('course_filter_nonce'); ?>',
                        query: query
                    },
                    beforeSend: function() {
                        $('#speaker-results').html('<div class="speaker-item">Searching...</div>').show();
                    },
                    success: function(response) {
                        if(response.success) {
                            if(response.data.length > 0) {
                                displaySpeakers(response.data);
                            } else {
                                $('#speaker-results').html('<div class="speaker-item">No matching speakers found</div>').show();
                            }
                        } else {
                            $('#speaker-results').hide().empty();
                        }
                    }
                });
            }, 300);
        });
        
        // Handle speaker selection
        $(document).on('click', '.speaker-item', function() {
            var speakerId = $(this).data('id');
            var speakerName = $(this).text();
            
            $('#selected-speaker-id').val(speakerId);
            $('#speaker-name').text(speakerName);
            $('#selected-speaker').show();
            $('#speaker-search').val('');
            $('#speaker-results').hide().empty();
            
            // Load courses immediately
            filterCourses(speakerId);
        });
        
        // Filter courses function
        function filterCourses(speakerId) {
            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                data: {
                    action: 'filter_courses',
                    nonce: '<?php echo wp_create_nonce('course_filter_nonce'); ?>',
                    author_id: speakerId
                },
                beforeSend: function() {
                    $('#course-results').html('<div class="loading-courses">Loading courses...</div>');
                },
                success: function(response) {
                    if(response.success) {
                        $('#author-info').html(response.data.author_info);
                        $('#course-results').html(response.data.courses);
                    } else {
                        $('#course-results').html('<p>Error loading courses</p>');
                    }
                }
            });
        }
    });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('course_filter_search', 'course_filter_search_shortcode');

// AJAX Handlers
add_action('wp_ajax_search_speakers', 'search_speakers_ajax_handler');
add_action('wp_ajax_nopriv_search_speakers', 'search_speakers_ajax_handler');
add_action('wp_ajax_get_all_speakers', 'get_all_speakers_ajax_handler');
add_action('wp_ajax_nopriv_get_all_speakers', 'get_all_speakers_ajax_handler');
add_action('wp_ajax_filter_courses', 'filter_courses_ajax_handler');
add_action('wp_ajax_nopriv_filter_courses', 'filter_courses_ajax_handler');

function get_all_speakers_ajax_handler() {
    check_ajax_referer('course_filter_nonce', 'nonce');

    global $wpdb;

    // Step 1: Get authors of published posts with meta _case27_listing_type = 'courses'
    $author_ids = $wpdb->get_col("
        SELECT DISTINCT p.post_author
        FROM {$wpdb->posts} p
        INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
        WHERE p.post_status = 'publish'
          AND pm.meta_key = '_case27_listing_type'
          AND pm.meta_value = 'courses'
    ");

    if (empty($author_ids)) {
        wp_send_json_success([]);
    }

    // Step 2: Get user data
    $args = array(
        'include' => $author_ids,
        'number'  => 200,
        'orderby' => 'display_name',
        'order'   => 'ASC',
        'fields'  => array('ID', 'display_name'),
    );

    $user_query = new WP_User_Query($args);
    $results = array();

    foreach ($user_query->get_results() as $user) {
        $results[] = array(
            'id'   => $user->ID,
            'name' => $user->display_name,
        );
    }

    wp_send_json_success($results);
}


// Get all speakers AJAX handler - Only users with published posts
// function get_all_speakers_ajax_handler() {
//     check_ajax_referer('course_filter_nonce', 'nonce');
    
//     $args = array(
//         'has_published_posts' => true, // Only users with published posts
//         'number' => 200, // Limit to 200 speakers for performance
//         'orderby' => 'display_name',
//         'order' => 'ASC',
//         'fields' => array('ID', 'display_name')
//     );
    
//     $user_query = new WP_User_Query($args);
//     $results = array();
    
//     foreach($user_query->get_results() as $user) {
//         $results[] = array(
//             'id' => $user->ID,
//             'name' => $user->display_name
//         );
//     }
    
//     wp_send_json_success($results);
// }

// Search speakers AJAX handler - Only users with published posts
function search_speakers_ajax_handler() {
    check_ajax_referer('course_filter_nonce', 'nonce');
    
    $query = sanitize_text_field($_POST['query']);
    $results = array();
    
    if(!empty($query)) {
        $args = array(
            'has_published_posts' => true, // Only users with published posts
            'search'         => '*' . $query . '*',
            'search_columns' => array('display_name', 'user_email'),
            'fields'         => array('ID', 'display_name'),
            'number'         => 100
        );
        
        $user_query = new WP_User_Query($args);
        
        foreach($user_query->get_results() as $user) {
            $results[] = array(
                'id' => $user->ID,
                'name' => $user->display_name
            );
        }
    }
    
    wp_send_json_success($results);
}

// Filter courses AJAX handler
function filter_courses_ajax_handler() {
    check_ajax_referer('course_filter_nonce', 'nonce');
    
    $author_id = intval($_POST['author_id']);
    
    $args = array(
        'post_type'      => 'job_listing',
        'author'         => $author_id,
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'meta_query'     => array(
            array(
                'key'     => '_case27_listing_type',
                'value'   => 'courses',
                'compare' => '=',
            )
        )
    );
    
    $query = new WP_Query($args);
    
    $author_info = '';
    if ($author_id) {
        $author = get_user_by('ID', $author_id);
        $author_info = '<div class="author-box">';
        $author_info .= '<h3>' . esc_html($author->display_name) . '</h3>';
        $author_info .= '<p>' . esc_html($author->description) . '</p>';
        $author_info .= '<p>Email: ' . esc_html($author->user_email) . '</p>';
        $author_info .= '</div>';
    }
    
    $courses = '';
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $course_id = get_the_ID();
            
            $courses .= '<div class="course-card">';
            
            // Course Image
            if (has_post_thumbnail()) {
                $courses .= '<div class="course-image">';
                $courses .= get_the_post_thumbnail($course_id, 'large');
                $courses .= '</div>';
            }
            
            $courses .= '<div class="course-content">';
            $courses .= '<h3>' . get_the_title() . '</h3>';
            $courses .= '<p>' . wp_trim_words(get_the_excerpt(), 25) . '</p>';
            $courses .= '<a href="' . get_permalink() . '" class="course-button">View Course</a>';
            $courses .= '</div>'; // .course-content
            $courses .= '</div>'; // .course-card
        }
    } else {
        $courses = '<div class="no-courses"><p>No courses found for this speaker.</p></div>';
    }
    
    wp_reset_postdata();
    
    wp_send_json_success(array(
        'author_info' => $author_info,
        'courses'     => $courses,
    ));
}
