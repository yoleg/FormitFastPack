<?php
/**
 * FormitFastPack
 *
 * Copyright 2011 by Oleg Pryadko (websitezen.com)
 *
 * This file is part of FormitFastPack, a FormIt helper pack for MODx Revolution
 * FormitFastPack is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * FormitFastPack is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along wit
 * FormitFastPack; if not, write to the Free Software Foundation, Inc., 59 Temple Place,
 * Suite 330, Boston, MA 02111-1307 USA

 * @package FormitFastPack
 */
/**
 * FormitFastPack build script
 *
 * @package FormitFastPack
 * @subpackage build
 */
$mtime = microtime();
$mtime = explode(" ", $mtime);
$mtime = $mtime[1] + $mtime[0];
$tstart = $mtime;
set_time_limit(0);

/* define package */
/* Set package info be sure to set all of these */
define('PKG_NAME','FormitFastPack');
define('PKG_NAME_LOWER','formitfastpack');
define('PKG_VERSION','1.0.0');
define('PKG_RELEASE','alpha');
define('PKG_CATEGORY','FormitFastPack');

/* Set package options - you can turn these on one-by-one
 * as you create the transport package
 * */
$hasMenu = false; /* Add items to the MODx Top Menu */
$hasSettings = false; /* Add new MODx System Settings */
$hasAccessPolicies = false;
$hasPolicyTemplates = false;
$hasAssets = false; /* Transfer the files in the assets dir. */
$hasCore = true;   /* Transfer the files in the core dir. */
$hasSnippets = true;
$hasChunks = false;
$hasSubPackages = false; /* add in other component packages (transport.zip files) - copy only, no auto-install */
$hasTemplates = false;
$hasPropertySets = false;
$hasResources = false;
$hasSystemSettings = false; /* Forces addition of some settings. */
$hasSetupOptions = false; /* Update system settings from PHP/ HTML form. */
$hasValidator = false; /* Run a validator before installing anything */
$hasMainResolver = false; /* Run a specific, general install resolver after installation */
$hasResolvers = false; /* Add additional custom resolvers */
$hasTemplateVariables = false;
$hasPlugins = false;
/* $hasPluginEvents = false; */

/* define sources */
$root = dirname(dirname(__FILE__)).'/';
$sources= array (
    'root' => $root,
    'build' => $root .'_build/',
    'resolvers' => $root . '_build/resolvers/',
    'data' => $root . '_build/data/',
    'events' => $root . '_build/data/events/',
    'permissions' => $root . '_build/data/permissions/',
    'properties' => $root . '_build/data/properties/',
    'validators'=> $root . '_build/validators/',
    'install_options' => $root . '_build/install.options/',
    'packages'=> $root . 'core/packages',
    'source_core' => $root . 'core/components/'.PKG_NAME_LOWER,
    'source_assets' => $root.'assets/components/'.PKG_NAME_LOWER,
    'plugins' => $root.'core/components/'.PKG_NAME_LOWER.'/elements/plugins/',
    'snippets' => $root.'core/components/'.PKG_NAME_LOWER.'/elements/snippets/',
    'lexicon' => $root . 'core/components/'.PKG_NAME_LOWER.'/lexicon/',
    'docs' => $root.'core/components/'.PKG_NAME_LOWER.'/docs/',
    'model' => $root.'core/components/'.PKG_NAME_LOWER.'/model/',
);
unset($root);

/* set package attributes options */
$packageAttributeArray = array(
    'license' => file_get_contents($sources['docs'] . 'license.txt'),
    'readme' => file_get_contents($sources['docs'] . 'readme.txt'),
    'changelog' => file_get_contents($sources['docs'] . 'changelog.txt'),
);
if ($hasSetupOptions) {
    $packageAttributeArray['setup-options'] = array();
    $packageAttributeArray['setup-options']['source'] = $sources['install_options'].'user.input.php';
}

/* override with your own defines here */
require_once ($sources['build'] . 'includes/functions.php');
require_once ($sources['build'] . 'build.config.php');
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';

/* start of work - get MODx */
$modx= new modX();
$modx->initialize('mgr');
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
$modx->setLogTarget('ECHO'); echo '<pre>'; flush();

/* load package builder */
$modx->loadClass('transport.modPackageBuilder','',false, true);
$builder = new modPackageBuilder($modx);
$builder->createPackage(PKG_NAME_LOWER,PKG_VERSION,PKG_RELEASE);
$builder->registerNamespace(PKG_NAME_LOWER,false,true,'{core_path}components/'.PKG_NAME_LOWER.'/');
$modx->getService('lexicon','modLexicon');
$modx->lexicon->load('formitfastpack:properties');

/* create new category w/ package name - required */
/** @var $category modCategory */
$category=$modx->newObject('modCategory');
$category->set('id',1);
$category->set('category',PKG_CATEGORY);
$modx->log(modX::LOG_LEVEL_INFO,'Packaged in category.'); flush();

/* add Resources */
if ($hasResources) {
    $resources = include $sources['data'].'transport.resources.php';
    if (!is_array($resources)) {
        $modx->log(modX::LOG_LEVEL_ERROR,'Could not package in resources.');
    } else {
        $attributes= array(
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::UNIQUE_KEY => 'pagetitle',
            xPDOTransport::RELATED_OBJECTS => true,
            xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array (
                'ContentType' => array(
                    xPDOTransport::PRESERVE_KEYS => false,
                    xPDOTransport::UPDATE_OBJECT => true,
                    xPDOTransport::UNIQUE_KEY => 'name',
                ),
            ),
        );
        foreach ($resources as $resource) {
            $vehicle = $builder->createVehicle($resource,$attributes);
            $builder->putVehicle($vehicle);
        }
        $modx->log(modX::LOG_LEVEL_INFO,'Packaged in '.count($resources).' resources.');
    }
    unset($resources,$resource,$attributes);
}

/* Transport Menus */
if ($hasMenu) {
    /* load menu */
    $modx->log(modX::LOG_LEVEL_INFO,'Packaging in menu...');
    $menu = include $sources['data'].'transport.menu.php';
    if (empty($menu)) {
        $modx->log(modX::LOG_LEVEL_ERROR,'Could not package in menu.');
    } else {
        $vehicle= $builder->createVehicle($menu,array (
        xPDOTransport::PRESERVE_KEYS => true,
        xPDOTransport::UPDATE_OBJECT => true,
        xPDOTransport::UNIQUE_KEY => 'text',
        xPDOTransport::RELATED_OBJECTS => true,
        xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array (
            'Action' => array (
                xPDOTransport::PRESERVE_KEYS => false,
                xPDOTransport::UPDATE_OBJECT => true,
                xPDOTransport::UNIQUE_KEY => array ('namespace','controller'),
            ),
        ),
        ));
        $builder->putVehicle($vehicle);

        $modx->log(modX::LOG_LEVEL_INFO,'Packaged in '.count($menu).' menu items.');
        unset($vehicle,$menu);
    }
}

/* load system settings */
if ($hasSettings) {
    $settings = include_once $sources['data'].'transport.settings.php';
    if (!is_array($settings)) {
        $modx->log(modX::LOG_LEVEL_ERROR,'Could not package in settings.');
    } else {
        $attributes= array(
            xPDOTransport::UNIQUE_KEY => 'key',
            xPDOTransport::PRESERVE_KEYS => true,
            xPDOTransport::UPDATE_OBJECT => false,
        );
        if (!is_array($settings)) { $modx->log(modX::LOG_LEVEL_FATAL,'Adding settings failed.'); }
        foreach ($settings as $setting) {
            $vehicle = $builder->createVehicle($setting,$attributes);
            $builder->putVehicle($vehicle);
        }
        $modx->log(modX::LOG_LEVEL_INFO,'Packaged in '.count($settings).' system settings.'); flush();
        unset($settings,$setting,$attributes);
    }
}

/* package in default access policy template */
if ($hasPolicyTemplates) {
    $templates = include $sources['data'].'transport.policytemplates.php';
    $attributes = array (
        xPDOTransport::PRESERVE_KEYS => false,
        xPDOTransport::UNIQUE_KEY => array('name'),
        xPDOTransport::UPDATE_OBJECT => true,
        xPDOTransport::RELATED_OBJECTS => true,
        xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array (
            'Permissions' => array (
                xPDOTransport::PRESERVE_KEYS => false,
                xPDOTransport::UPDATE_OBJECT => true,
                xPDOTransport::UNIQUE_KEY => array ('template','name'),
            ),
        )
    );
    if (is_array($templates)) {
        foreach ($templates as $template) {
            $vehicle = $builder->createVehicle($template,$attributes);
            $builder->putVehicle($vehicle);
        }
        $modx->log(modX::LOG_LEVEL_INFO,'Packaged in '.count($templates).' Access Policy Templates.'); flush();
    } else {
        $modx->log(modX::LOG_LEVEL_ERROR,'Could not package in Access Policy Templates.');
    }
    unset ($templates,$template,$idx,$ct,$attributes);
}

/* package in default access policy */
if ($hasAccessPolicies) {
    $attributes = array (
        xPDOTransport::PRESERVE_KEYS => false,
        xPDOTransport::UNIQUE_KEY => array('name'),
        xPDOTransport::UPDATE_OBJECT => true,
    );
    $policies = include $sources['data'].'transport.policies.php';
    if (!is_array($policies)) {
        $modx->log(modX::LOG_LEVEL_FATAL,'Adding policies failed.');
    } else {
        foreach ($policies as $policy) {
            $vehicle = $builder->createVehicle($policy,$attributes);
            $builder->putVehicle($vehicle);
        }
        $modx->log(modX::LOG_LEVEL_INFO,'Packaged in '.count($policies).' Access Policies.'); flush();
        unset($policies,$policy,$attributes);
    }
}

/* add plugins - DONE */
if ($hasPlugins) {
    $plugins = include $sources['data'].'transport.plugins.php';
    if (!is_array($plugins)) { $modx->log(modX::LOG_LEVEL_FATAL,'Adding plugins failed.'); }
    $attributes= array(
        xPDOTransport::UNIQUE_KEY => 'name',
        xPDOTransport::PRESERVE_KEYS => false,
        xPDOTransport::UPDATE_OBJECT => true,
        xPDOTransport::RELATED_OBJECTS => true,
        xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array (
            'PluginEvents' => array(
                xPDOTransport::PRESERVE_KEYS => true,
                xPDOTransport::UPDATE_OBJECT => false,
                xPDOTransport::UNIQUE_KEY => array('pluginid','event'),
            ),
        ),
    );
    foreach ($plugins as $plugin) {
        $vehicle = $builder->createVehicle($plugin, $attributes);
        $builder->putVehicle($vehicle);
    }
    $modx->log(modX::LOG_LEVEL_INFO,'Packaged in '.count($plugins).' plugins.'); flush();
    unset($plugins,$plugin,$attributes);
}

/* add snippets */
if ($hasSnippets) {
    $modx->log(modX::LOG_LEVEL_INFO,'Adding in snippets.');
    $snippets = include $sources['data'].'transport.snippets.php';
    if (is_array($snippets)) {
        $category->addMany($snippets,'Snippets');
    } else { $modx->log(modX::LOG_LEVEL_FATAL,'Adding snippets failed.'); }
    $modx->log(modX::LOG_LEVEL_INFO,'Packaged in '.count($snippets).' snippets.'); flush();
    unset($snippets);
}

/* add chunks */
if ($hasChunks) { /* add chunks  */
    $modx->log(modX::LOG_LEVEL_INFO,'Adding in chunks.');
    /* note: Chunks' default properties are set in transport.chunks.php */
    $chunks = include $sources['data'].'transport.chunks.php';
    if (is_array($chunks)) {
        $category->addMany($chunks, 'Chunks');
    } else { $modx->log(modX::LOG_LEVEL_FATAL,'Adding chunks failed.'); }
}

/* add templates  */
if ($hasTemplates) {
    $modx->log(modX::LOG_LEVEL_INFO,'Adding in templates.');  flush();
    /* note: Templates' default properties are set in transport.templates.php */
    $templates = include $sources['data'].'transport.templates.php';
    if (is_array($templates)) {
        if (! $category->addMany($templates,'Templates')) {
            $modx->log(modX::LOG_LEVEL_INFO,'addMany failed with templates.');  flush();
        };
    } else { $modx->log(modX::LOG_LEVEL_FATAL,'Adding templates failed.'); }
}

/* add templatevariables  */
if ($hasTemplateVariables) {
    $modx->log(modX::LOG_LEVEL_INFO,'Adding in Template Variables.');  flush();
    /* note: Template Variables' default properties are set in transport.tvs.php */
    $templatevariables = include $sources['data'].'transport.tvs.php';
    if (is_array($templatevariables)) {
        $category->addMany($templatevariables, 'TemplateVars');
    } else { $modx->log(modX::LOG_LEVEL_FATAL,'Adding templatevariables failed.'); }
}

/* add property sets */
if ($hasPropertySets) {
    $modx->log(modX::LOG_LEVEL_INFO,'Adding in property sets.');  flush();
    $propertysets = include $sources['data'].'transport.propertysets.php';
    /* note: property set' properties are set in transport.propertysets.php */
    if (is_array($snippets)) {
        $category->addMany($propertysets, 'PropertySets');
    } else { $modx->log(modX::LOG_LEVEL_FATAL,'Adding property sets failed.'); }
}

/* Create Category attributes array dynamically
 * based on which elements are present
 */

$attr = array(
    xPDOTransport::UNIQUE_KEY => 'category',
    xPDOTransport::PRESERVE_KEYS => false,
    xPDOTransport::UPDATE_OBJECT => true,
    xPDOTransport::RELATED_OBJECTS => true,
);
if ($hasValidator) {
    $attr[xPDOTransport::ABORT_INSTALL_ON_VEHICLE_FAIL] = true;
}
if ($hasSnippets) {
    $attr[xPDOTransport::RELATED_OBJECT_ATTRIBUTES]['Snippets'] = array(
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::UNIQUE_KEY => 'name',
        );
}
if ($hasPropertySets) {
    $attr[xPDOTransport::RELATED_OBJECT_ATTRIBUTES]['PropertySets'] = array(
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::UNIQUE_KEY => 'name',
        );
}
if ($hasChunks) {
    $attr[xPDOTransport::RELATED_OBJECT_ATTRIBUTES]['Chunks'] = array(
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::UNIQUE_KEY => 'name',
        );
}
if ($hasPlugins) {
    $attr[xPDOTransport::RELATED_OBJECT_ATTRIBUTES]['Plugins'] = array(
        xPDOTransport::PRESERVE_KEYS => false,
        xPDOTransport::UPDATE_OBJECT => true,
        xPDOTransport::UNIQUE_KEY => 'name',
    );
}
if ($hasTemplates) {
    $attr[xPDOTransport::RELATED_OBJECT_ATTRIBUTES]['Templates'] = array(
        xPDOTransport::PRESERVE_KEYS => false,
        xPDOTransport::UPDATE_OBJECT => true,
        xPDOTransport::UNIQUE_KEY => 'templatename',
    );
}
if ($hasTemplateVariables) {
    $attr[xPDOTransport::RELATED_OBJECT_ATTRIBUTES]['TemplateVars'] = array(
        xPDOTransport::PRESERVE_KEYS => false,
        xPDOTransport::UPDATE_OBJECT => true,
        xPDOTransport::UNIQUE_KEY => 'name',
    );
}
/* create a vehicle for the category and all the things
 * we've added to it.
 */
$vehicle = $builder->createVehicle($category,$attr);

/* copy core folder */
if ($hasCore) {
    $modx->log(modX::LOG_LEVEL_INFO, 'Adding in core.'); flush();
    $vehicle->resolve('file',array(
        'source' => $sources['source_core'],
        'target' => "return MODX_CORE_PATH . 'components/';",
    ));
}

/* copy assets folder */
if ($hasAssets) {
    $modx->log(modX::LOG_LEVEL_INFO, 'Adding in assets.');  flush();
    $vehicle->resolve('file',array(
        'source' => $sources['source_assets'],
        'target' => "return MODX_ASSETS_PATH . 'components/';",
    ));
}

/* Add subpackages */
/* The transport.zip files will be copied to core/packages
 * but will have to be installed manually with "Add New Package and
 *  "Search Locally for Packages" in Package Manager
 */

if ($hasSubPackages) {
    $modx->log(modX::LOG_LEVEL_INFO, 'Adding in subpackages.');  flush();
     $vehicle->resolve('file',array(
        'source' => $sources['packages'],
        'target' => "return MODX_CORE_PATH;",
        ));
}

/* setupOptions Resolver */
if ($hasSetupOptions) {
    $vehicle->resolve('php',array(
        'source' => $sources['resolvers'] . 'setupoptions.resolver.php',
    ));
}

if ($hasValidator) {
    $modx->log(modX::LOG_LEVEL_INFO,'Adding in Script Validator.');
    $vehicle->validate('php',array(
        'source' => $sources['validators'] . 'preinstall.script.php',
    ));
}

if ($hasMainResolver) {
    $modx->log(modX::LOG_LEVEL_INFO,'Adding in General Resolver.');
    $vehicle->resolve('php',array(
        'source' => $sources['resolvers'] . 'general.resolver.php',
    ));
}

if ($hasSystemSettings) {
    $modx->log(modX::LOG_LEVEL_INFO, 'Adding in some default settings.');
    $vehicle->resolve('php',array(
        'source' => $sources['resolvers'] . 'system_settings.resolver.php',
    ));
}

/* resolvers */
if ($hasResolvers) {
    // add as many other resolvers as necessary
    $modx->log(modX::LOG_LEVEL_INFO, 'Adding in all other resolvers.');
    $vehicle->resolve('php',array(
        'source' => $sources['resolvers'] . 'tables.resolver.php',
    ));
}
$modx->log(modX::LOG_LEVEL_INFO,'Packaged in resolvers.'); flush();

/* Put the category vehicle (with all the stuff we added to the
 * category) into the package
 */
$builder->putVehicle($vehicle);

/* now pack in the license file, readme and setup options */
$builder->setPackageAttributes($packageAttributeArray);
$modx->log(modX::LOG_LEVEL_INFO,'Packaged in package attributes.'); flush();

$modx->log(modX::LOG_LEVEL_INFO,'Packing...'); flush();
$builder->pack();

$mtime= microtime();
$mtime= explode(" ", $mtime);
$mtime= $mtime[1] + $mtime[0];
$tend= $mtime;
$totalTime= ($tend - $tstart);
$totalTime= sprintf("%2.4f s", $totalTime);

$modx->log(modX::LOG_LEVEL_INFO,"\n<br />Package Built.<br />\nExecution time: {$totalTime}\n");

exit ();