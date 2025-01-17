<?php
namespace Opencart\Catalog\Model\Extension\Opencart\Payment;
/**
 * Class FreeCheckout
 *
 * @package Opencart\Catalog\Model\Extension\Opencart\Payment
 */
class FreeCheckout extends \Opencart\System\Engine\Model {
	/**
	 * @param array<mixed> $address
	 *
	 * @return array<string, mixed>
	 */
	public function getMethods(array $address = []): array {
		$this->load->language('extension/opencart/payment/free_checkout');

		// Order Totals
		$totals = [];
		$taxes = $this->cart->getTaxes();
		$total = 0;

		$this->load->model('checkout/cart');

		($this->model_checkout_cart->getTotals)($totals, $taxes, $total);

		if (!empty($this->session->data['vouchers'])) {
			$amounts = array_column($this->session->data['vouchers'], 'amount');
		} else {
			$amounts = [];
		}

		$total += array_sum($amounts);

		if ($this->currency->format($total, $this->config->get('config_currency'), false, false) <= 0.00) {
			$status = true;
		} elseif ($this->cart->hasSubscription()) {
			$status = false;
		} else {
			$status = false;
		}

		$method_data = [];

		if ($status) {
			$option_data['free_checkout'] = [
				'code' => 'free_checkout.free_checkout',
				'name' => $this->language->get('heading_title')
			];

			$method_data = [
				'code'       => 'free_checkout',
				'name'       => $this->language->get('heading_title'),
				'option'     => $option_data,
				'sort_order' => $this->config->get('payment_free_checkout_sort_order')
			];
		}

		return $method_data;
	}
}
