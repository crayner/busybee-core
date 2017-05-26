<?php
namespace Busybee\PaginationBundle\Model ;

use Busybee\PaginationBundle\Form\PaginationType;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Query;
use Symfony\Component\HttpFoundation\Request ;
use Symfony\Component\DependencyInjection\ContainerInterface as Container ;
use Doctrine\ORM\EntityRepository ;
use stdClass ;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Router;

/**
 * Pagination Manager
 *
 * @version	25th October 2016
 * @since	25th October 2016
 * @author	Craig Rayner
 */
abstract class PaginationManager implements PaginationInterface
{
    /**
     * @var EntityRepository
     */
    protected $repository ;

    /**
     * @var ObjectManager
     */
    protected $manager ;

    /**
     * @var Container
     */
    protected $container ;

    /**
     * @var Query
     */
    protected $query ;

    /**
     * @var string
     */
    protected $result;

    /**
     * @var array
     */
    private $initialSettings ;

    /**
     * @var
     */
    private $form ;

    /**
     * @var stdClass
     */
    private $setting;

    /**
     * @var integer
     */
    private $limit;

    /**
     * @var integer
     */
    private $lastLimit;

    /**
     * @var array
     */
    private $searchList;

    /**
     * @var string
     */
    private $search;

    /**
     * @var integer
     */
    private $total;

    /**
     * @var integer
     */
    private $offSet;

    /**
     * @var integer
     */
    private $pages;

    /**
     * @var string
     */
    private $choice;

    /**
     * @var string
     */
    private $choices;

    /**
     * @var string
     */
    private $lastSearch;

    /**
     * @var array
     */
    private $control = array();

    /**
     * @var array
     */
    private $join = array();

    /**
     * @var array
     */
    private $select = array();

    /**
     * @var string
     */
    private $alias = 'z';

    /**
     * @var array
     */
    private $sortBy = [];

    /**
     * @var Session
     */
    private $session;

    /**
     * @var Router
     */
    private $router;

    /**
     * @var Router
     */
    private $reDirect;

    /**
     * Constructor
     *
     * @version	25th October 2016
     * @since	25th October 2016
     * @param	array	$pagination  Pagination Settings from Parameters
     * @param	EntityRepository	$repository
     * @param   Container $container
     */
    public function __construct($pagination, EntityRepository $repository, Container $container)
    {
        $this->setPagination($pagination);
        $this->manager = $container->get('doctrine')->getManager();
        $this->repository = $repository ;
        $this->session = $container->get('session');
        $this->router = $container->get('router');

        $params = [];
        $params['route'] = parse_url($container->get('request_stack')->getCurrentRequest()->getUri(), PHP_URL_PATH);
        $this->path = $params['route'];
        $this->setChoice(null);
        $this->setReDirect(false);

        $this->form = $container->get('form.factory')->createNamedBuilder('paginator', PaginationType::class, $this, $params)->getForm();
    }

    /**
     * Set Pagination
     *
     * @version	25th October 2016
     * @since	25th October 2016
     * @param	array	$pagination  Pagination Settings from Parameters
     * @return	void
     */
    private function setPagination($pagination)
    {
        $this->initialiseSettings();
        $this->initialSettings = $pagination;

        if (! is_array($pagination)) return ;
        foreach ($pagination as $name => $value) {
            $setName = 'set'.ucwords($name);
            $this->setting->$name = $value ;
            $this->$setName($value);
        }
        $this->total = 0;
    }

    private function initialiseSettings()
    {
        $this->setting = new stdClass();

        $pagination = [];
        $pagination['alias'] = 'default';
        $pagination['sortBy'] = [];
        $pagination['limit'] = 25;
        $pagination['searchList'] = [];
        $pagination['choices'] = [];
        $pagination['join'] = [];
        $pagination['select'] = [];

        foreach ($pagination as $name => $value) {
            $setName = 'set' . ucwords($name);
            $this->setting->$name = $value;
            $this->$setName($value);
        }
        $this->total = 0;
    }

    /**
     * get Data Set
     *
     * @version	25th October 2016
     * @since	25th October 2016
     * @return	array	of Data
     */
    public function getDataSet()
    {
        $this->pages = intval(ceil($this->getTotal() / $this->getLimit())) ;
        $this->result = $this->buildQuery()
            ->setFirstResult($this->getOffSet())
            ->setMaxResults($this->getLimit())
            ->getQuery()
            ->getResult();
        $this->writeSession();
        return $this->result;
    }

    /**
     * get Total
     *
     * @version	25th October 2016
     * @since	25th October 2016
     * @return    integer
     */
    public function getTotal()
    {
        if (empty($this->total))
            $this->total = intval($this->buildQuery(true)
                ->getQuery()
                ->getSingleScalarResult());
        return $this->total;
    }

    /**
     * set Total
     *
     * @version	25th October 2016
     * @since	25th October 2016
     * @param	integer		$total
     * @return    PaginationManager
     */
    public function setTotal($total)
    {
        $this->total = intval($total) > 0 ? intval($total) : 0;
        return $this;
    }

    /**
     * get Limit
     *
     * @version	25th October 2016
     * @since	25th October 2016
     * @return    PaginationManager
     */
    public function getLimit()
    {
        $this->limit = intval($this->limit) < 10 ? 10 : intval($this->limit);
        return $this->limit ;
    }

    /**
     * set Limit
     *
     * @version	25th October 2016
     * @since	25th October 2016
     * @param	integer		$limit
     * @return    PaginationManager
     */
    public function setLimit($limit)
    {
        $limit = intval($limit);
        $this->limit = $limit < 10 ? 10 : $limit ;
        return $this ;
    }

    /**
     * get OffSet
     *
     * @version	25th October 2016
     * @since	25th October 2016
     * @return	integer
     */
    public function getOffSet()
    {
        return $this->offSet = !empty($this->offSet) ? $this->offSet : 0;
    }

    /**
     * set OffSet
     *
     * @version	25th October 2016
     * @since	25th October 2016
     * ^ @param	integer		$offSet
     * @return    PaginationManager
     */
    public function setOffSet($offSet)
    {
        $this->offSet = empty($offSet) ? 0 : intval($offSet);
        return $this ;
    }

    /**
     * @return $this
     */
    private function writeSession()
    {
        $pag = empty($this->session->get('pagination')) ? [] : $this->session->get('pagination');

        $cc = empty($pag[$this->paginationName]) ? [] : $pag[$this->paginationName];

        $cc['limit'] = $this->limit;
        $cc['search'] = $this->search;
        $cc['offSet'] = $this->offSet;
        $cc['choice'] = false !== $this->reDirect ? $this->reDirect : $this->choice;
        $cc['sortBy'] = $this->sortBy;

        $pag[$this->paginationName] = $cc;

        $this->session->set('pagination', $pag);

        return $this;
    }

    /**
     * get Sort By Name
     *
     * @version	25th October 2016
     * @since	25th October 2016
     * @return	string
     */
    public function getSortByName()
    {
        return $this->sortBy ;
    }

    /**
     * set Order By
     *
     * @version	25th October 2016
     * @since	25th October 2016
     * @return    PaginationManager
     */
    public function setOrderBy()
    {
        if (! empty($this->getSortBy())) {
            foreach($this->getSortBy() as $name=> $order) {
                $this->query->addOrderBy($name , $order);
            }
        }
        return $this ;
    }

    /**
     * get Sort By
     *
     * @version	25th October 2016
     * @since	25th October 2016
     * @return	array
     */
    public function getSortBy()
    {
        if (!empty($this->setting->sortBy[$this->sortBy]))
            return $this->setting->sortBy[$this->sortBy];
        return [];
    }

    /**
     * set Sort By
     *
     * @version    25th October 2016
     * @since    25th October 2016
     * @param    array /string    $sortBy
     * @return    PaginationManager
     */
    public function setSortBy($sortBy)
    {
        if (is_array($sortBy)) {
            reset($sortBy);
            $sortBy = key($sortBy);
        }
        if (array_key_exists($sortBy, $this->setting->sortBy))
            $this->sortBy = $sortBy;
        else {
            reset($this->setting->sortBy);
            $this->sortBy = key($this->setting->sortBy);
        }
        return $this;
    }

    /**
     * set Search Where
     *
     * @version	25th October 2016
     * @since	25th October 2016
     * @return	string
     */
    public function setSearchWhere()
    {
        if (!is_null($this->getSearch())) {
            $x = 0;
            foreach($this->getSearchList() as $field) {
                $this->query->orWhere($field . ' LIKE :search' . $x);
                $this->query->setParameter('search'. $x++, '%'.$this->getSearch().'%');
            }
        }
        return $this ;
    }

    /**
     * get Search
     *
     * @version	25th October 2016
     * @since	25th October 2016
     * @return	string
     */
    public function getSearch()
    {
        return $x = empty($this->search) ? null : '%' . $this->search . '%';
    }

    /**
     * set Search
     *
     * @version	25th October 2016
     * @since	25th October 2016
     * @param    array $search
     * @return    PaginationManager
     */
    public function setSearch($search)
    {
        $this->search = filter_var($search);
        return $this ;
    }

    /**
     * get Search List
     *
     * @version	25th October 2016
     * @since	25th October 2016
     * @return  array
     */
    public function getSearchList()
    {
        return $this->searchList = is_array($this->searchList) ? $this->searchList : array() ;
    }

    /**
     * set Search List
     *
     * @version	25th October 2016
     * @since	25th October 2016
     * @param	array	$searchList
     * @return    PaginationManager
     */
    public function setSearchList($searchList)
    {
        $this->searchList = is_array($searchList) ? $searchList : array();
        return $this ;
    }

    /**
     * get Search Property
     *
     * @version    23rd May 2017
     * @since    23rd May 2017
     * @return    string
     */
    public function getSearchProperty()
    {
        return empty($this->search) ? 'null' : $this->search;
    }

    /**
     * get Pages
     *
     * @version	25th October 2016
     * @since	25th October 2016
     * @return	query
     */
    public function getPages()
    {
        $this->pages = intval($this->pages) < 1 ? 1 : intval($this->pages) ;
        return $this->pages;
    }

    /**
     * get Sort List
     *
     * @version	25th October 2016
     * @since	25th October 2016
     * @return	array
     */
    public function getSortList()
    {
        $sortByList = array();
        if (! empty($this->setting->sortBy) && is_array($this->setting->sortBy))
            foreach($this->setting->sortBy as $name=> $w)
                $sortByList[$name] = $name;
        return $sortByList;
    }

    /**
     * inject Request
     *
     * @version    25th October 2016
     * @since    25th October 2016
     * @param Request $request
     * @return PaginationManager
     */
    public function injectRequest(Request $request)
    {
        $this->getForm()->handleRequest($request);
        if (! $this->form->isSubmitted()) {

            $this->post = false;
            $this->resetPagination();
            $last = $this->session->get('pagination');
            if (!empty($last[$this->paginationName])) {
                $last = $last[$this->paginationName];
                $this->setSearch($last['search']);
                $this->form['currentSearch']->setData($last['search']);
                $this->setLimit($last['limit']);
                $this->form['limit']->setData($last['limit']);
                $this->setLastLimit($last['limit']);
                $this->form['lastLimit']->setData($last['limit']);
                $this->setting->limit = $last['limit'];
                $this->setOffSet($last['offSet']);
                $this->setChoice($last['choice']);
                if ($last['choice'] !== $this->path)
                    $this->setReDirect($last['choice']);
                $this->setSortBy($last['sortBy']);
            }

            $this->getTotal();
        } else {

            $this->post = true;
            $this->setSearch($this->form['currentSearch']->getData());
            $this->setLastSearch($this->form['lastSearch']->getData());
            $this->setTotal($this->form['total']->getData());
            $this->setOffSet($this->form['offSet']->getData());
            if ($this->form->has('lastChoice') && !empty($this->form['lastChoice']->getData()) && $this->form['lastChoice']->getData() !== $this->form['choice']->getData())
                $this->resetPagination();
            if (trim($this->getSearch(), '%') !== trim($this->getLastSearch(), '%'))
                $this->resetPagination();
            if ($this->form->has('choice'))
                $this->setChoice($this->form['choice']->getData());
            else
                $this->setChoice(null);
            $this->setSearch($this->form['currentSearch']->getData());
            $this->setLastSearch($this->form['lastSearch']->getData());
            $this->setLimit($this->form['limit']->getData());
            $this->setLastLimit($this->form['lastLimit']->getData());
            $this->setSortBy($this->form['currentSort']->getData());
            $this->getTotal();
            $this->managePost();
        }
        $this->form = $this->getForm()->createView();
        return $this;
    }

    /**
     * get Form
     *
     * @version	25th October 2016
     * @since	25th October 2016
     * @return	Object
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * Reset Pagination
     *
     * @version	25th October 2016
     * @since	25th October 2016
     * @return    PaginationManager
     */
    private function resetPagination()
    {
        $this->setPagination($this->initialSettings);
        $this->total = 0;
        $this->offSet = 0;
        return $this ;
    }

    /**
     * get Last Search
     *
     * @version	25th October 2016
     * @since	25th October 2016
     * @return	string
     */
    public function getLastSearch()
    {
        return $this->lastSearch = empty($this->lastSearch) ? '' : $this->lastSearch ;
    }

    /**
     * set Last Search
     *
     * @version    25th October 2016
     * @since    25th October 2016
     * @param $lastSearch
     * @return PaginationManager
     */
    public function setLastSearch($lastSearch)
    {
        $this->lastSearch = $lastSearch ;
        if (empty($lastSearch))
            $this->lastSearch = null;
        return $this ;
    }

    /**
     * Manage Post
     *
     * @version    15th February 2017
     * @since	25th October 2016
     * @return    PaginationManager
     */
    public function managePost()
    {
        $data = $this->form->getExtraData();

        if (array_key_exists('prev', $data))
            $this->getPrev();
        if (array_key_exists('next', $data))
            $this->getNext();
        // if ajax is used then ....
        switch ($this->control) {
            case 'paginatorNext':
                $this->getNext();
                break;
            case 'paginatorPrev':
                $this->getPrev();
                break;
        }
        return $this ;
    }

    /**
     * get Prev
     *
     * @version	25th October 2016
     * @since	25th October 2016
     * @return    integer
     */
    private function getPrev()
    {
        $offSet = $this->getOffSet() - $this->getLimit();
        return $this->checkOffset($offSet) ;
    }

    /**
     * check OffSet
     *
     * @version    25th October 2016
     * @since    25th October 2016
     * @param   integer $offSet
     * @return int
     */
    private function checkOffSet($offSet)
    {
        if ($offSet >= $this->total)
            $offSet = $this->total - $this->getLimit();
        if ($offSet < 0)
            $offSet = 0;
        $this->setOffset($offSet);
        return $offSet ;
    }

    /**
     * get Next
     *
     * @version	25th October 2016
     * @since	25th October 2016
     * @return    integer
     */
    private function getNext()
    {
        $offSet = $this->getOffSet() + $this->getLimit();
        if ($offSet > $this->getTotal())
            $offSet = $this->getOffSet();
        return $this->checkOffSet($offSet) ;
    }

    /**
     * get Current Page
     *
     * @version	25th October 2016
     * @since	25th October 2016
     * @return	integer
     */
    public function getCurrentPage()
    {
        return intval($this->getOffSet() / $this->getLimit()) + 1;
    }

    /**
     * get Total Pages
     *
     * @version	25th October 2016
     * @since	25th October 2016
     * @return	integer
     */
    public function getTotalPages()
    {
        return $this->pages;
    }

    /**
     * get Result
     *
     * @version	25th October 2016
     * @since	25th October 2016
     * @return    array Result
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * get Last Limit
     *
     * @version	25th October 2016
     * @since	25th October 2016
     * @return    integer
     */
    public function getLastLimit()
    {
        $this->lastLimit = intval($this->lastLimit) < 10 ? 10 : intval($this->lastLimit);
        return $this->lastLimit ;
    }

    /**
     * set Last Limit
     *
     * @version	25th October 2016
     * @since	25th October 2016
     * @param	integer		$limit
     * @return    PaginationManager
     */
    public function setLastLimit($limit)
    {
        $limit = intval($limit);
        $this->lastLimit = $limit < 10 ? 10 : $limit ;
        return $this ;
    }

    /**
     * @return int
     */
    public function getFirstRecord()
    {
        return $this->offSet + 1;
    }

    /**
     * @return int
     */
    public function getLastRecord()
    {
        return $this->offSet + $this->getLimit() > $this->getTotal() ? $this->getTotal() : $this->offSet + $this->getLimit();
    }

    public function getChoices()
    {
        return $this->choices;
    }

    public function setChoices($choices)
    {
        $this->choices = $choices;
        $this->setting->choices = $choices;

        return $this;
    }

    public function getChoice()
    {
        if (is_array($this->choice))
            throw new \InvalidArgumentException('NO NO NO Choice is not an array ever.');

        return $this->choice;
    }

    public function setChoice($choice)
    {
        if (is_array($choice))
            throw new \InvalidArgumentException('NO NO NO Choice is not an array ever, so don\'t set it that way');

        $this->choice = $choice;

        return $this;
    }

    public function getReDirect()
    {
        return $this->reDirect;
    }

    public function setReDirect($x)
    {
        $this->reDirect = $x;

        return $this;
    }

    public function getLastChoice()
    {
        return $this->choice;
    }

    /**
     * initiate Query
     *
     * @version	25th October 2016
     * @since	25th October 2016
     * @param	boolean		$count
     * @return	Query
     */
    protected function initiateQuery($count = false)
    {
        $this->query = $this->repository->createQueryBuilder($this->getAlias());
        if ($count)
            $this->query->select('COUNT('.$this->getAlias().')');
        return $this->query ;
    }

    /**
     * set Select
     *
     * @version	27th October 2016
     * @since	27th October 2016
     * @return    string
     */
    public function getAlias()
    {
        return $this->alias ;
    }

    /**
     * set Join
     *
     * @version	27th October 2016
     * @since	27th October 2016
     * @return    PaginationManager
     */
    protected function setQueryJoin()
    {
        if (! is_array($this->join)) return $this ;
        foreach ($this->join as $name=> $pars) {
            $type = empty($pars['type']) ? 'join' : $pars['type'] ;
            $this->query->$type($name, $pars['alias']);
        }
        return $this ;
    }

    /**
     * set Query Select
     *
     * @version	27th October 2016
     * @since	27th October 2016
     * @return    PaginationManager
     */
    protected function setQuerySelect()
    {
        dump($this);
        if (!is_array($this->select) || empty($this->select)) return $this;
        foreach ($this->select as $name)
            $this->query->addSelect($name);
        return $this ;
    }

    /**
     * set Join
     *
     * @version	27th October 2016
     * @since	27th October 2016
     * @return    PaginationManager
     */
    private function setJoin($join) // Scripted Call
    {
        $this->join = $join;
        return $this ;
    }

    /**
     * set Select
     *
     * @version	27th October 2016
     * @since	27th October 2016
     * @return    PaginationManager
     */
    private function setSelect($select) // Scripted Call
    {
        $this->select = $select;
        return $this ;
    }

    /**
     * set Alias
     *
     * @version	27th October 2016
     * @since	27th October 2016
     * @return    PaginationManager
     */
    private function setAlias($alias) // Scripted Call
    {
        $this->alias = $alias;
        return $this ;
    }
}