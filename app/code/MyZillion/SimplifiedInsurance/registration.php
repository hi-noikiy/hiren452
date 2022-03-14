<?php
/**
 * Copyright (c) 2020, Zillion Insurance Services, Inc.
 * All rights reserved.
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *   * Redistributions of source code must retain the above copyright notice,
 *     this list of conditions and the following disclaimer.
 *   * Redistributions in binary form must reproduce the above copyright notice,
 *     this list of conditions and the following disclaimer in the documentation
 *     and/or other materials provided with the distribution.
 *   * Neither the name of Zend Technologies USA, Inc. nor the names of its
 *     contributors may be used to endorse or promote products derived from this
 *     software without specific prior written permission.
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
 * ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
 * ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

//@codingStandardsIgnoreFile
// Add PHP wrapper MyZillion_InsuranceApi to autoload
$apiModuleRoute = dirname(__DIR__) . '/InsuranceApi/src';

if (file_exists($apiModuleRoute)) {
    if (!defined('BP')) {
        define('BP', dirname(dirname(dirname(dirname(__DIR__)))));
    }

    if (!defined('VENDOR_PATH')) {
        define('VENDOR_PATH', BP . '/app/etc/vendor_path.php');
        if (!file_exists(VENDOR_PATH)) {
            throw new \Exception(
                'We can\'t read some files that are required to run the Magento application. '
                . 'This usually means file permissions are set incorrectly.'
            );
        }
    }

    $vendorDir      = include VENDOR_PATH;
    $vendorAutoload = BP . "/{$vendorDir}/autoload.php";
    $loader         = include $vendorAutoload;
    // Only add InsuranceApi if it isn't already set
    if (!isset($loader->getPrefixesPsr4()['MyZillion\\InsuranceApi\\'])) {
        $loader->setPsr4('MyZillion\\InsuranceApi\\', [$apiModuleRoute]);
    }
}

// Register MyZillion_SimplifiedInsurance
\Magento\Framework\Component\ComponentRegistrar::register(
    \Magento\Framework\Component\ComponentRegistrar::MODULE,
    'MyZillion_SimplifiedInsurance',
    __DIR__
);
