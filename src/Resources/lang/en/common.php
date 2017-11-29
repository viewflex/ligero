<?php

return [

    ## Characters or Abbreviations

    'symbol'                => [
        'null'                  => 'n/a',
        'range'                 => '~',
    ],

    ## General Labels for UI Controls

    'label'                 => [
        'login'                 => 'Login',
        'logout'                => 'Logout',
        'register'              => 'Register',
        'profile'               => 'My Profile',
        'items'                 => 'Items',
        'users'                 => 'Users',
        'reports'               => 'Reports',
        'statistics'            => 'Statistics',
        'settings'              => 'Settings',
        'goto'                  => 'Go to',
        'show_json'             => 'Display as JSON',

        'new'                   => 'New',
        'cancel'                => 'Cancel',
        'save'                  => 'Save',
        'view'                  => 'View',
        'edit'                  => 'Edit',
        'update'                => 'Update',
        'delete'                => 'Delete',

        'search'                => 'Search',
        'sort'                  => 'Sort',
        'all'                   => 'All',
        'select_all'            => 'Select All',
        'duplicate'             => 'Duplicate',
        'new_record'            => 'New Record...',

        'back'                  => 'Back',
        'continue'              => 'Continue',
        'yes'                   => 'Yes',
        'no'                    => 'No',
        'errors'                => 'Error|Errors',
        'message'               => 'Message',
    ],


    ## Current Results Page

    'results'               => [
        'viewing'               => 'Viewing',
        'items'                 => 'Item|Items',
        'records'               => 'Record|Records',
        'page'                  => 'Page',
        'of'                    => 'of',
    ],


    ## Nav

    'nav'                   => [
        'first'                 => 'First',
        'previous'              => 'Previous',
        'next'                  => 'Next',
        'last'                  => 'Last',
        'list'                  => 'List',
        'grid'                  => 'Grid',
        'item'                  => 'Item',
        'view'                  => 'View',
        'per_page'              => 'per page',
        'view_as'               => 'View as',
    ],


    ## Nav Tooltips

    'tooltip'               => [
        'first'                 => 'Go to first page',
        'previous'              => 'Go to previous page',
        'next'                  => 'Go to next page',
        'last'                  => 'Go to last page',
        'list'                  => 'View as a list',
        'grid'                  => 'View as a grid',
        'item'                  => 'View single item',
    ],


    ## Contextual Titles

    'title'                 => [
        'new_domain_record'     => 'New :Domain Record',
        'viewing_domain_record' => 'Viewing :Domain Record',
        'update_domain_record'  => 'Update :Domain Record',
    ],


    ## Operation Result Messages

    'msg'                   => [
        'records_affected_by'   => 'Records affected by action :action: :affected',
        'item_created'          => 'Item was created successfully.',
        'item_not_created'      => 'Action failed. Item was not created.',
        'item_updated'          => 'Item was updated successfully.',
        'item_not_updated'      => 'Action failed. Item was not updated.',
        'item_deleted'          => 'Item was deleted successfully.',
        'item_not_deleted'      => 'Action failed. Item was not deleted.',
        'no_data_affected'      => 'No data was affected.',
        'item_create_cancelled' => 'New record was cancelled. No data was saved.',
        'item_edit_cancelled'   => 'Edit cancelled. No data was affected.',
        'search_failed'         => 'Search failed. Try different criteria.',
        'data_modified'         => 'Data was modified, clearing previous query.'
    ],

    ## Prompts

    'prompt'                => [
        'confirm_delete' => 'Are you sure you want to delete? This cannot be reversed.',
        'confirm_cancel' => 'Are you sure you want to cancel changes?',
    ],


    /*
    |--------------------------------------------------------------------------
    | Domain Localization Defaults
    |--------------------------------------------------------------------------
    |
    | Override to customize for a given publisher domain.
    |
    */

    ## Labels for columns and values

    'id' => 'ID',
    'active' => 'Active',
    'name' => 'Name',
    'category' => 'Category',
    'subcategory' => 'Subcategory',
    'description' => 'Description',
    'created_at' => 'Created At',
    'updated_at' => 'Updated At',


    ## Strings for values of boolean fields

    'active_true' => 'Active',
    'active_false' => 'Inactive',


];
