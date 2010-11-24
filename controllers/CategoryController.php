<?php

class CategoryControllerCore extends FrontController
{
	public function setMedia()
	{
		parent::setMedia();
		Tools::addCSS(array(
			_PS_CSS_DIR_.'jquery.cluetip.css' => 'all',
			_THEME_CSS_DIR_.'scenes.css' => 'all',
			_THEME_CSS_DIR_.'category.css' => 'all',
			_THEME_CSS_DIR_.'product_list.css' => 'all'
		));
		Tools::addJS(_THEME_JS_DIR_.'products-comparison.js');
	}
	
	public function displayHeader()
	{
		parent::displayHeader();
		$this->productSort();
	}

	public function process()
	{
		parent::process();
		if (!isset($_GET['id_category']) OR !Validate::isUnsignedId($_GET['id_category']))
			$this->errors[] = Tools::displayError('category ID is missing');
		else
		{
			$category = new Category((int)(Tools::getValue('id_category')), (int)($this->cookie->id_lang));
			if (!Validate::isLoadedObject($category))
				$this->errors[] = Tools::displayError('category does not exist');
			elseif (!$category->checkAccess((int)($this->cookie->id_customer)))
				$this->errors[] = Tools::displayError('you do not have access to this category');
			else
			{
				$rewrited_url = $this->link->getCategoryLink($category->id, $category->link_rewrite);
				
				/* Scenes  (could be externalised to another controler if you need them */
				$this->smarty->assign('scenes', Scene::getScenes((int)($category->id), (int)($this->cookie->id_lang), true, false));

				/* Scenes images formats */
				if ($sceneImageTypes = ImageType::getImagesTypes('scenes'))
				{
					foreach ($sceneImageTypes AS $sceneImageType)
					{
						if ($sceneImageType['name'] == 'thumb_scene')
							$thumbSceneImageType = $sceneImageType;
						elseif ($sceneImageType['name'] == 'large_scene')
							$largeSceneImageType = $sceneImageType;
					}
					$this->smarty->assign('thumbSceneImageType', isset($thumbSceneImageType) ? $thumbSceneImageType : NULL);
					$this->smarty->assign('largeSceneImageType', isset($largeSceneImageType) ? $largeSceneImageType : NULL);
				}
				
				$category->description = nl2br2($category->description);
				$subCategories = $category->getSubCategories((int)($this->cookie->id_lang));
				$this->smarty->assign('category', $category);
				if (Db::getInstance()->numRows())
				{
					$this->smarty->assign('subcategories', $subCategories);
					$this->smarty->assign(array(
						'subcategories_nb_total' => sizeof($subCategories),
						'subcategories_nb_half' => ceil(sizeof($subCategories) / 2)
					));
				}
				if ($category->id != 1)
				{
					$nbProducts = $category->getProducts(NULL, NULL, NULL, $this->orderBy, $this->orderWay, true);
					$this->pagination($nbProducts);
					$this->smarty->assign('nb_products', $nbProducts);
					$cat_products = $category->getProducts((int)($this->cookie->id_lang), (int)($this->p), (int)($this->n), $this->orderBy, $this->orderWay);
				}
				$this->smarty->assign(array(
					'products' => (isset($cat_products) AND $cat_products) ? $cat_products : NULL,
					'id_category' => (int)($category->id),
					'id_category_parent' => (int)($category->id_parent),
					'return_category_name' => Tools::safeOutput($category->name),
					'path' => Tools::getPath((int)($category->id), $category->name),
					'add_prod_display' => Configuration::get('PS_ATTRIBUTE_CATEGORY_DISPLAY'),
					'homeSize' => Image::getSize('home')
				));
			}
		}

		$this->smarty->assign(array(
			'allow_oosp' => (int)(Configuration::get('PS_ORDER_OUT_OF_STOCK')),
			'comparator_max_item' => (int)(Configuration::get('PS_COMPARATOR_MAX_ITEM')),
			'suppliers' => Supplier::getSuppliers()
		));
	}
	
	public function displayContent()
	{
		parent::displayContent();
		$this->smarty->display(_PS_THEME_DIR_.'category.tpl');
	}
}

?>