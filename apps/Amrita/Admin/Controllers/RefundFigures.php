<?php
namespace Amrita\Admin\Controllers;

class RefundFigures extends \Admin\Controllers\BaseAuth
{

    protected $list_route = '/admin/amrita/refundfigures';

    protected $get_item_route = '/admin/amrita/refundfigures/view/{id}';

    protected $edit_item_route = '/admin/amrita/refundfigures/view/{id}';

    public function index()
    {
        $model = new \Amrita\Models\Import\RefundFigures();
        $state = $model->populateState()->getState();
        \Base::instance()->set('state', $state);
        
        $paginated = $model->paginate();
        \Base::instance()->set('paginated', $paginated);
        
        $this->app->set('meta.title', 'Refund Figures | Amrita');
        
        $view = \Dsc\System::instance()->get('theme');
        echo $view->render('Amrita\Admin\Views::refundfigures/list.php');
    }

    public function export()
    {
        $id = \Base::instance()->get('PARAMS.id');
        
        try
        {
            $item = $this->getItem();
            if (empty($item->id))
            {
                throw new \Exception('Invalid ID');
            }
            
            $model = new \Amrita\Models\Export\RefundFigures();
            $item = $model->exportItem($item);
            
            \Dsc\System::addMessage($item->processing_result);
        }
        catch (\Exception $e)
        {
            \Dsc\System::addMessage($e->getMessage(), 'error');
        }
        
        $this->app->reroute($this->list_route);
    }

    public function view()
    {
        $f3 = \Base::instance();
        $id = $this->inputfilter->clean($f3->get('PARAMS.id'), 'alnum');
        $record = (new \Amrita\Models\Import\RefundFigures())->load(array(
            '_id' => new \MongoId($id)
        ));
        \Base::instance()->set('item', $record);
        
        $this->app->set('meta.title', 'Refund Figure | Amrita');
        
        $view = \Dsc\System::instance()->get('theme');
        echo $view->render('Amrita\Admin\Views::refundfigures/view.php');
    }

    /**
     * Update existing record
     */
    public function update()
    {
        $data = \Base::instance()->get('REQUEST');
        
        $this->doUpdate($data);
        
        if ($route = $this->getRedirect())
        {
            \Base::instance()->reroute($route);
        }
        
        return;
    }

    protected function getModel()
    {
        $model = new \Amrita\Models\Import\RefundFigures();
        return $model;
    }

    protected function getItem()
    {
        $f3 = \Base::instance();
        $id = $this->inputfilter->clean($f3->get('PARAMS.id'), 'alnum');
        $model = $this->getModel()->setState('filter.id', $id);
        
        try
        {
            $item = $model->getItem();
        }
        catch (\Exception $e)
        {
            \Dsc\System::instance()->addMessage("Invalid Item: " . $e->getMessage(), 'error');
            $f3->reroute($this->list_route);
            return;
        }
        
        return $item;
    }

    protected function doUpdate(array $data, $key = null)
    {
        if (empty($this->list_route))
        {
            throw new \Exception('Must define a route for listing the items');
        }
        
        if (empty($this->edit_item_route))
        {
            throw new \Exception('Must define a route for editing the item');
        }
        
        if (!isset($data['submitType']))
        {
            $data['submitType'] = "save_edit";
        }
        
        $f3 = \Base::instance();
        $flash = \Dsc\Flash::instance();
        $this->item = $this->getItem();
        
        // save
        $save_as = false;
        try
        {
            $values = $data;
            unset($values['submitType']);
            
            if (!empty($values['__shop']['product_id']))
            {
                $this->item->{'shop.product_id'} = new \MongoId($values['__shop']['product_id']);
            }
            else
            {
                unset($this->item->{'shop.product_id'});
            }
            
            if (!empty($values['__shop']['variant_id']))
            {
                $this->item->{'shop.variant_id'} = $values['__shop']['variant_id'];
            }
            else
            {
                unset($this->item->{'shop.variant_id'});
            }
            
            $this->item->save();
            \Dsc\System::instance()->addMessage('Item updated', 'success');
        }
        catch (\Exception $e)
        {
            \Dsc\System::instance()->addMessage('Save failed with the following errors:', 'error');
            \Dsc\System::instance()->addMessage($e->getMessage(), 'error');
            if (\Base::instance()->get('DEBUG'))
            {
                \Dsc\System::instance()->addMessage($e->getTraceAsString(), 'error');
            }
            
            if ($f3->get('AJAX'))
            {
                // output system messages in response object
                return $this->outputJson($this->getJsonResponse(array(
                    'error' => true,
                    'message' => \Dsc\System::instance()->renderMessages()
                )));
            }
            
            // redirect back to the edit form with the fields pre-populated
            \Dsc\System::instance()->setUserState('use_flash.' . $this->edit_item_route, true);
            $flash->store($data);
            $id = $this->item->get('id');
            $route = str_replace('{id}', $id, $this->edit_item_route);
            
            $this->setRedirect($route);
            
            return false;
        }
        
        // redirect to the editing form for the new item
        if (method_exists($this->item, 'cast'))
        {
            $this->item_data = $this->item->cast();
        }
        else
        {
            $this->item_data = \Joomla\Utilities\ArrayHelper::fromObject($this->item);
        }
        
        if ($f3->get('AJAX'))
        {
            return $this->outputJson($this->getJsonResponse(array(
                'message' => \Dsc\System::instance()->renderMessages(),
                'result' => $this->item_data
            )));
        }
        
        switch ($data['submitType'])
        {
            case "save_close":
                $route = $this->list_route;
                break;
            default:
                $flash->store($this->item_data);
                $id = $this->item->get('id');
                $route = str_replace('{id}', $id, $this->edit_item_route);
                break;
        }
        
        $this->setRedirect($route);
        
        return $this;
    }

    public function fetchAndSave()
    {
        $model = new \Amrita\Models\Import\RefundFigures();
        $model->doFetchAndSave();
        
        $model = new \Amrita\Models\Export\RefundFigures();
        $model->doFetchAndSave();
        
        $message = 'Check the System logs for results';
        \Dsc\System::addMessage($message, 'success');
        
        $this->app->reroute('/admin/amrita/refundfigures');
    }
}
?>