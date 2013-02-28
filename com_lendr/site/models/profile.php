<?php // no direct access

defined( '_JEXEC' ) or die( 'Restricted access' ); 
 
class LendrModelsProfile extends LendrModelsDefault
{

  //Define class level variables
  var $_user_id     = null;

  function __construct()
  {

    $app = JFactory::getApplication();

    //If no User ID is set to current logged in user
    if ( $app->input->get('profile_id') )
    {
      $this->_user_id = $app->input->get('profile_id');
    }
    if(is_null($this->_user_id))
    {
      $user = JFactory::getUser();
      $this->_user_id = $user->get('id');
    }

    

    parent::__construct();       
  }
 
  protected function _buildQuery()
  {
    $db = JFactory::getDBO();
    $query = $db->getQuery(TRUE);

    $query->select("u.id, u.username, u.name, u.email, u.registerDate");
    $query->from("#__users as u");

    $query->select("COUNT(b.book_id) as totalBooks");
    $query->leftjoin("#__lendr_books as b on b.user_id = u.id");

    $query->select("COUNT(r.review_id) as totalReviews");
    $query->leftjoin("#__lendr_reviews as r on r.user_id = u.id");

    return $query;
  }

  protected function _buildWhere($query)
  {

    return $query;
  }

  function getItem()
  {

    $profile = JFactory::getUser($this->_user_id);
    $userDetails = JUserHelper::getProfile($this->_user_id);
    $profile->details =  isset($userDetails->profile) ? $userDetails->profile : array();

    $libraryModel = new LendrModelsLibrary();
    $libraryModel->set('_user_id',$this->_user_id);
    $profile->library = $libraryModel->getItem();

    $waitlistModel = new LendrModelsWaitlist();
    $waitlistModel->set('_waitlist', TRUE);
    $profile->waitlist = $waitlistModel->getItem();

    $profile->isMine = JFactory::getUser()->id == $profile->id ? TRUE : FALSE;

    return $profile;
  }

}