<?php


namespace App\Constants;


class EndPoints
{


    /**
     * ========================================================================
     * Common Services
     * ========================================================================
     */
    const register = '/register';
    const login = '/login';
    const forgotPassword = '/forgotPassword';
    const forgotPasswordCustom = '/forgotPasswordCustom';

    const detail = '/detail';
    const profile = '/profile';
    const changePassword = '/changePassword';
    const updateProfile = '/updateProfile';
    const logout = '/logout';
    const list = '/list';

    /**
     * ========================================================================
     * Admin Services
     * ========================================================================
     */
    const create_tenant = '/createTenant';


    /**
     * ========================================================================
     * Users Services
     * ========================================================================
     */
    const user_register = '/user/register';
    const user_login = '/user/login';
    const user_forgotPassword = '/user/forgotPassword';
    const user_forgotPasswordCustom = '/user/forgotPasswordCustom';
    const user_adminUsers = '/user/adminUsers';
    const user_detail = '/user/detail';
    const user_profile = '/user/profile';
    const user_changePassword = '/user/changePassword';
    const user_updateProfile = '/user/updateProfile';
    const user_logout = '/user/logout';
    const user_list = '/user/list';
    const user_activeUsers = '/user/activeUsers';
    const user_newUsers = '/user/newUsers';
    const user_blockUsers = '/user/blockUsers';


    /**
     * ========================================================================
     * Middeleware Route
     * ========================================================================
     */
    const unauthorised = 'unauthorised';
    const adminaccess = 'adminaccess';
    const activeaccess = 'activeaccess';
    const password_reset = 'password/reset';
    const password_update = 'password/update';

}
