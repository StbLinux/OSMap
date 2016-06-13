<?php
/**
 * @package   OSMap
 * @copyright 2007-2014 XMap - Joomla! Vargas - Guillermo Vargas. All rights reserved.
 * @copyright 2016 Open Source Training, LLC. All rights reserved.
 * @contact   www.alledia.com, support@alledia.com
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

use Alledia\OSMap;

defined('_JEXEC') or die();


class OSMapControllerSitemapItems extends OSMap\Controller\Form
{
    /**
     * Method override to check if the user can edit an existing record.
     *
     * @param    array    An array of input data.
     * @param    string   The name of the key for the primary key.
     *
     * @return   boolean
     */
    protected function _allowEdit($data = array(), $key = 'id')
    {
        // Initialise variables.
        $recordId = (int) isset($data[$key]) ? $data[$key] : 0;

        // Assets are being tracked, so no need to look into the category.
        return JFactory::getUser()->authorise('core.edit', 'com_osmap.sitemap.' . $recordId);
    }

    public function cancel($key = NULL)
    {
        $this->setRedirect(JRoute::_('index.php?option=com_osmap&view=sitemaps'));
    }

    public function apply()
    {
        $this->save();

        $sitemapId = OSMap\Factory::getApplication()->input->getInt('id');

        $this->setRedirect(JRoute::_('index.php?option=com_osmap&view=sitemapitems&id=' . $sitemapId));
    }

    public function save($key = NULL, $urlVar = NULL)
    {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $app = OSMap\Factory::getApplication();

        $sitemapId  = $app->input->getInt('id');
        $updateData = $app->input->getRaw('update-data');

        $model = $this->getModel();

        if (!empty($updateData)) {
            $updateData = json_decode($updateData, true);

            if (!empty($updateData) && is_array($updateData)) {
                foreach ($updateData as $data) {
                    $row = $model->getTable();
                    $row->load(
                        array(
                            'sitemap_id' => $sitemapId,
                            'uid'        => $data->uid
                        )
                    );

                    $data['sitemap_id'] = $sitemapId;

                    $row->save($data);
                }
            }
        }

        $this->setRedirect(JRoute::_('index.php?option=com_osmap&view=sitemaps'));
    }
}