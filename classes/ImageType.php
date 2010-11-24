<?php

/**
* ImageType class, ImageType.php
* Image types management
* @category classes
*
* @author PrestaShop <support@prestashop.com>
* @copyright PrestaShop
* @license http://www.opensource.org/licenses/osl-3.0.php Open-source licence 3.0
* @version 1.4
*
*/

class ImageTypeCore extends ObjectModel
{
	public		$id;

	/** @var string Name */
	public		$name;

	/** @var integer Width */
	public		$width;

	/** @var integer Height */
	public 		$height;

	/** @var boolean Apply to products */
	public		$products;

	/** @var integer Apply to categories */
	public 		$categories;

	/** @var integer Apply to manufacturers */
	public 		$manufacturers;

	/** @var integer Apply to suppliers */
	public 		$suppliers;

	/** @var integer Apply to scenes */
	public 		$scenes;

	protected $fieldsRequired = array('name', 'width', 'height');
	protected $fieldsValidate = array(
		'name' => 'isImageTypeName',
		'width' => 'isImageSize',
		'height' => 'isImageSize',
		'categories' => 'isBool',
		'products' => 'isBool',
		'manufacturers' => 'isBool',
		'suppliers' => 'isBool',
		'scenes' => 'isBool'
	);
	protected $fieldsSize = array('name' => 16);

	protected $table = 'image_type';
	protected $identifier = 'id_image_type';

	/**
	 * @var array Image types cache
	 */
	protected static $images_types_cache = array();

	public function getFields()
	{
		parent::validateFields();
		$fields['name'] = pSQL($this->name);
		$fields['width'] = (int)($this->width);
		$fields['height'] = (int)($this->height);
		$fields['products'] = (int)($this->products);
		$fields['categories'] = (int)($this->categories);
		$fields['manufacturers'] = (int)($this->manufacturers);
		$fields['suppliers'] = (int)($this->suppliers);
		$fields['scenes'] = (int)($this->scenes);
		return $fields;
	}

	/**
	* Returns image type definitions
	*
	* @param string|null Image type
	* @return array Image type definitions
	*/
	static public function getImagesTypes($type = NULL)
	{
		if (!isset(self::$images_types_cache[$type]))
		{
			if (!empty($type))
				$where = 'WHERE ' . pSQL($type) . ' = 1 ';
			else
				$where = '';

			$query = 'SELECT * FROM `'._DB_PREFIX_.'image_type`'.$where.'ORDER BY `name` ASC';
			self::$images_types_cache[$type] = Db::getInstance()->ExecuteS($query);
		}

		return self::$images_types_cache[$type];
	}

	/**
	* Check if type already is already registered in database
	*
	* @param string $typeName Name
	* @return integer Number of results found
	*/
	static public function typeAlreadyExists($typeName)
	{
		if (!Validate::isImageTypeName($typeName))
			die(Tools::displayError());
			
		$result = Db::getInstance()->ExecuteS('
		SELECT `id_image_type`
		FROM `'._DB_PREFIX_.'image_type`
		WHERE `name` = \''.pSQL($typeName).'\'');

		return Db::getInstance()->NumRows();
	}

	/**
	 * Finds image type definition by name and type
	 * @param string $name
	 * @param string $type
	 */
	static public function getByNameNType($name, $type)
	{
		return Db::getInstance()->getRow('SELECT `id_image_type`, `name`, `width`, `height`, `products`, `categories`, `manufacturers`, `suppliers`, `scenes` FROM `'._DB_PREFIX_.'image_type` WHERE `name` = \''.pSQL($name).'\' AND `'.pSQL($type).'` = 1');
	}

}