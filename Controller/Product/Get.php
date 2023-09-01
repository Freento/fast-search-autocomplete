<?php

declare(strict_types=1);

namespace Freento\FastSearchAutocomplete\Controller\Product;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\Action\HttpGetActionInterface;

class Get implements HttpGetActionInterface
{
    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
     * @var RedirectFactory
     */
    private RedirectFactory $redirectFactory;

    /**
     * @var ProductRepositoryInterface
     */
    private ProductRepositoryInterface $productRepository;

    /**
     * @var UrlInterface
     */
    private UrlInterface $url;

    /**
     * @param RequestInterface $request
     * @param RedirectFactory $redirectFactory
     * @param ProductRepositoryInterface $productRepository
     * @param UrlInterface $url
     */
    public function __construct(
        RequestInterface $request,
        RedirectFactory $redirectFactory,
        ProductRepositoryInterface $productRepository,
        UrlInterface $url
    ) {
        $this->request = $request;
        $this->redirectFactory = $redirectFactory;
        $this->productRepository = $productRepository;
        $this->url = $url;
    }

    /**
     * Get product url by id and redirect
     *
     * @return Redirect
     */
    public function execute(): Redirect
    {
        $redirect = $this->redirectFactory->create();
        $productId = $this->request->getParam('id');
        if (!$productId) {
            return $redirect->setPath($this->url->getUrl('noroute'));
        }

        try {
            $product = $this->productRepository->getById($productId);
            return $redirect->setPath($product->getProductUrl());
        } catch (\Exception) {
            return $redirect->setPath($this->url->getUrl('noroute'));
        }
    }
}
