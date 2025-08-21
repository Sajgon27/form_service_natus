<?php
/**
 * Polish date helper functions for Serwis Natu Plugin
 * These functions help display dates in Polish format
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get month name in Polish
 * 
 * @param int $month_number Month number (1-12)
 * @return string Month name in Polish
 */
function serwis_natu_get_polish_month($month_number) {
    $polish_months = array(
        1 => 'stycznia',
        2 => 'lutego',
        3 => 'marca',
        4 => 'kwietnia',
        5 => 'maja',
        6 => 'czerwca',
        7 => 'lipca',
        8 => 'sierpnia',
        9 => 'września',
        10 => 'października',
        11 => 'listopada',
        12 => 'grudnia'
    );
    
    return $polish_months[$month_number];
}

/**
 * Format date in Polish format
 * 
 * @param string|int $date Date string or timestamp
 * @return string Formatted date in Polish
 */
function serwis_natu_format_polish_date($date) {
    if (!is_numeric($date)) {
        $timestamp = strtotime($date);
    } else {
        $timestamp = $date;
    }
    
    if ($timestamp === false) {
        return '';
    }
    
    $day = date('j', $timestamp);
    $month_num = date('n', $timestamp);
    $year = date('Y', $timestamp);
    $time = date('H:i', $timestamp);
    
    $month_name = serwis_natu_get_polish_month($month_num);
    
    return sprintf('%d %s %s, %s', $day, $month_name, $year, $time);
}
