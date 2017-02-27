<?php

/**
 * TechDivision\Import\Category\Ee\Observers\EeCategoryUpdateObserver
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-category-ee
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Category\Ee\Observers;

use TechDivision\Import\Utils\EntityStatus;
use TechDivision\Import\Category\Ee\Utils\MemberNames;
use TechDivision\Import\Category\Observers\CategoryUpdateObserver;

/**
 * Observer that create's the category itself for the Magento 2 EE edition.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-category-ee
 * @link      http://www.techdivision.com
 */
class EeCategoryUpdateObserver extends CategoryUpdateObserver
{

    /**
     * Process the observer's business logic.
     *
     * @return array The processed row
     */
    protected function process()
    {

        // prepare the static entity values
        $product = $this->initializeProduct($this->prepareAttributes());

        // insert the entity and set the entity ID, SKU and attribute set
        $this->setLastRowId($this->persistProduct($product));
        $this->setLastEntityId($product[MemberNames::ENTITY_ID]);
    }

    /**
     * Initialize the product with the passed attributes and returns an instance.
     *
     * @param array $attr The category attributes
     *
     * @return array The initialized category
     */
    protected function initializeProduct(array $attr)
    {

        // initialize the category attributes
        $attr = parent::initializeCategory($attr);

        // query whether or not, we found a new product
        if ($attr[EntityStatus::MEMBER_NAME] === EntityStatus::STATUS_CREATE) {
            // if yes, initialize the additional Magento 2 EE category values
            $additionalAttr = array(
                MemberNames::ENTITY_ID  => $this->nextIdentifier(),
                MemberNames::CREATED_IN => 1,
                MemberNames::UPDATED_IN => strtotime('+20 years')
            );

            // merge and return the attributes
            $attr = array_merge($attr, $additionalAttr);
        }

        // otherwise simply return the attributes
        return $attr;
    }

    /**
     * Set's the row ID of the category that has been created recently.
     *
     * @param string $rowId The row ID
     *
     * @return void
     */
    protected function setLastRowId($rowId)
    {
        $this->getSubject()->setLastRowId($rowId);
    }

    /**
     * Return's the next available category entity ID.
     *
     * @return integer The next available category entity ID
     */
    protected function nextIdentifier()
    {
        return $this->getSubject()->nextIdentifier();
    }
}
