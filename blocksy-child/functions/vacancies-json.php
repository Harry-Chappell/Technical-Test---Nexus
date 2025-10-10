<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // direct access
}

/**
 * Generate a JSON file containing all 'vacancies' posts.
 *
 * Each vacancy includes:
 * - id (post ID)
 * - title
 * - link (permalink)
 * - closing_date, ref, salary (custom fields)
 * - taxonomies: full-part, job-type, location, sector (as arrays of term objects)
 */
function bchild_generate_vacancies_json() {
    $args = array(
        'post_type'      => 'vacancies',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
    );

    $query = new WP_Query( $args );
    $vacancies = array();

    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $post_id = get_the_ID();

            $item = array(
                'id'           => $post_id,
                'title'        => get_the_title( $post_id ),
                'link'         => get_permalink( $post_id ),
                'closing_date' => get_post_meta( $post_id, 'closing_date', true ) ?: '',
                'ref'          => get_post_meta( $post_id, 'ref', true ) ?: '',
                'salary'       => get_post_meta( $post_id, 'salary', true ) ?: '',
            );

            $taxonomies = array( 'full-part', 'job-type', 'location', 'sector' );
            foreach ( $taxonomies as $tax ) {
                $terms = get_the_terms( $post_id, $tax );
                if ( $terms && ! is_wp_error( $terms ) ) {
                    $mapped = array_map( function( $t ) {
                        return array(
                            'id'   => (int) $t->term_id,
                            'name' => $t->name,
                            'slug' => $t->slug,
                        );
                    }, $terms );
                } else {
                    $mapped = array();
                }
                $item[ $tax ] = $mapped;
            }

            $vacancies[] = $item;
        }
        wp_reset_postdata();
    }

    $json = wp_json_encode( $vacancies, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );

    // Write to a single file atomically: vacancies.json in the child theme directory.
    $file = get_stylesheet_directory() . '/vacancies' . date('ymdHis') . '.json';
    $dir = dirname( $file );

    // ensure directory exists (should already exist for theme, but be safe)
    if ( ! file_exists( $dir ) ) {
        wp_mkdir_p( $dir );
    }

    // create a temp file in the same directory to allow atomic rename
    $temp = $file . '.' . time() . '.' . wp_rand( 1000, 9999 ) . '.tmp';
    $result = @file_put_contents( $temp, $json );
    if ( $result === false ) {
        error_log( 'bchild_generate_vacancies_json: Failed to write temp file ' . $temp );
        return false;
    }

    // Try an atomic rename; if it fails, fallback to copy+unlink
    if ( ! @rename( $temp, $file ) ) {
        if ( ! @copy( $temp, $file ) || ! @unlink( $temp ) ) {
            error_log( 'bchild_generate_vacancies_json: Failed to move temp file to ' . $file );
            // attempt to remove the temp file if it still exists
            if ( file_exists( $temp ) ) {
                @unlink( $temp );
            }
            return false;
        }
    }

    // Cleanup any older timestamped vacancy JSON files (e.g., vacancies*.json)
    $pattern = $dir . '/vacancies*.json';
    $files = glob( $pattern );
    if ( $files ) {
        foreach ( $files as $f ) {
            // keep the current file
            if ( realpath( $f ) === realpath( $file ) ) {
                continue;
            }
            // only unlink files that match the expected prefix (glob already matches)
            @unlink( $f );
        }
    }

    return true;
}

/**
 * Hook into post save to regenerate JSON when vacancies are added/updated.
 */
function bchild_vacancies_maybe_generate_on_save( $post_id, $post, $update ) {
    // only act on the vacancies post type
    if ( empty( $post ) || get_post_type( $post_id ) !== 'vacancies' ) {
        return;
    }

    // skip autosaves and revisions
    if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
        return;
    }

    bchild_generate_vacancies_json();
}
add_action( 'save_post', 'bchild_vacancies_maybe_generate_on_save', 10, 3 );

/**
 * Regenerate JSON when a vacancy is deleted, trashed or untrashed.
 */
function bchild_vacancies_maybe_generate_on_delete( $post_id ) {
    $post_type = get_post_type( $post_id );
    if ( $post_type !== 'vacancies' ) {
        return;
    }

    bchild_generate_vacancies_json();
}
add_action( 'deleted_post', 'bchild_vacancies_maybe_generate_on_delete' );
add_action( 'trashed_post', 'bchild_vacancies_maybe_generate_on_delete' );
add_action( 'untrash_post', 'bchild_vacancies_maybe_generate_on_delete' );
