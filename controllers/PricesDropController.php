<?php

class PricesDropControllerCore extends FrontController
{
	public function setMedia()
	{
		parent::setMedia();
		Tools::addCSS(_THEME_CSS_DIR_.'product_list.css');
	}
	
	public function process()
	{
		parent::process();
		
		$this->productSort();
		$nbProducts = Product::getPricesDrop((int)($this->cookie->id_lang), NULL, NULL, true);
		$this->pagination($nbProducts);
		
		$this->smarty->assign(array(
			'products' => Product::getPricesDrop((int)($this->cookie->id_lang), (int)($this->p) - 1, (int)($this->n), false, $this->orderBy, $this->orderWay),
			'add_prod_display' => Configuration::get('PS_ATTRIBUTE_CATEGORY_DISPLAY'),
			'nbProducts' => $nbProducts));
	}
	
	public function displayContent()
	{
		parent::displayContent();
		$this->smarty->display(_PS_THEME_DIR_.'prices-drop.tpl');
	}
}

?>