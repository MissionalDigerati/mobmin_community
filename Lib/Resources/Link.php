<?php
/**
 * This file is part of #MobMin Community.
 * 
 * #MobMin Community is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * Joshua Project API is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see 
 * <http://www.gnu.org/licenses/>.
 *
 * @author Johnathan Pulos <johnathan@missionaldigerati.org>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * 
 */
namespace Resources;

/**
 * The Link Resource for managing links to the Pligg site
 */
class Link extends Model
{
    /**
     * The \Resources\Tag Resource object
     *
     * @var \Resources\Tag
     * @access protected
     **/
    protected $tagResource;
    /**
     * The \Resources\Total Resource object
     *
     * @var \Resources\Total
     * @access protected
     **/
    protected $totalResource;
    /**
     * The table name to query
     *
     * @var string
     * @access protected
     **/
    protected $tableName = 'links';
    /**
     * The primary key of the table
     *
     * @var string
     * @access protected
     **/
    protected $primaryKey = 'link_id';
    /**
     * An array of whitelisted attributes
     *
     * @var array
     * @access protected
     **/
    protected $accessibleAttributes = array(
        'link_author', 'link_status', 'link_randkey', 'link_votes', 'link_karma', 'link_modified', 'link_date',
        'link_published_date', 'link_category', 'link_url', 'link_url_title', 'link_title', 'link_title_url',
        'link_content', 'link_summary', 'link_tags', 'social_media_id'
    );
    /**
     * A whitelist of all allowable link status
     *
     * @var array
     * @access protected
     **/
    protected $whitelistLinkStatuses = array('published', 'new', 'discard');
    /**
     * The length to truncate the content to in order to create the summary
     *
     * @var integer
     * @access protected
     **/
    protected $summaryLength = 150;
    /**
     * Construct the model object
     *
     * @param \PDO $db The database connection
     * @param \Resources\Tag $tagObject The tag object
     * @return void
     * @throws InvalidArgumentException if $db is not a \PDO Object
     * @throws InvalidArgumentException if $tagObject is not a \Resources\Tag Object
     * @author Johnathan Pulos
     **/
    public function __construct($db, $tagObject, $totalObject)
    {
        parent::__construct($db);
        $this->setTagObject($tagObject);
        $this->setTotalObject($totalObject);
    }
    /**
     * Set the \Resources\Tag Object
     *
     * @param \Resources\Tag $tagObject The \Resources\Tag Object
     * @return void
     * @access protected
     * @throws InvalidArgumentException if $tagObject is not a \Resources\Tag Object
     * @author Johnathan Pulos
     **/
    protected function setTagObject($tagObject)
    {
        if (is_a($tagObject, '\Resources\Tag')) {
            $this->tagResource = $tagObject;
        } else {
            throw new \InvalidArgumentException('$tagObject must be of the class \Resources\Tag.');
            exit;
        }
    }
    /**
     * Set the \Resources\Total Object
     *
     * @param \Resources\Total $totalObject The \Resources\Total Object
     * @return void
     * @access protected
     * @throws InvalidArgumentException if $totalObject is not a \Resources\Total Object
     * @author Johnathan Pulos
     **/
    protected function setTotalObject($totalObject)
    {
        if (is_a($totalObject, '\Resources\Total')) {
            $this->totalResource = $totalObject;
        } else {
            throw new \InvalidArgumentException('$totalObject must be of the class \Resources\Total.');
            exit;
        }
    }
    /**
     * Insert/Update the link in the database.  Pass an id to update.
     *
     * @param array $data an array of the link data to save
     * @param integer $id the Link.link_id of the record to update
     * @return boolean Did it save the data?
     * @access public
     * @author Johnathan Pulos
     * @todo Implement update script
     **/
    public function save($data, $id = null)
    {
        if (is_null($id)) {
            $data['link_summary'] = $this->createSummary($data['link_content']);
            if ($saved = $this->insertRecord($data)) {
                $this->saveTags($data);
                $this->totalResource->increment($data['link_status']);
            }
        }
        return $saved;
    }
    /**
     * Save the tags for the link
     *
     * @param array $data The link data
     * @return void
     * @access private
     * @author Johnathan Pulos
     **/
    private function saveTags($data)
    {
        $linkId = $this->getLastID();
        $tagList = explode(',', $data['link_tags']);
        if (($data['link_tags'] != '') && (count($tagList) > 0)) {
            foreach ($tagList as $singleTag) {
                $tagData = array(
                    'tag_link_id'   =>  $linkId,
                    'tag_date'      =>  date('Y-m-d H:i:s',time()),
                    'tag_words'     =>  trim(strip_tags($singleTag)),
                );
                $this->tagResource->save($tagData);
            }
        }
    }
    /**
     * prepare the attribute before binding to the PDOStatement
     *
     * @param string $key The attribute name
     * @param mixed $value The given value to save
     * @return mixed The final prepared value
     * @access protected
     * @author Johnathan Pulos
     * @throws InvalidArgumentException if $key = 'link_author' is 0, null, or empty
     * @throws InvalidArgumentException if $key = 'link_status' is not in $this->whitelistLinkStatuses
     **/
    protected function prepareAttribute($key, $value)
    {
        $newValue = parent::prepareAttribute($key, $value);
        switch ($key) {
            case 'link_author':
                $newValue = intval($newValue);
                if (($newValue == 0) || ($newValue == null) || ($newValue == '')) {
                    throw new \InvalidArgumentException("Attribute link_author must be a valid user id.");
                    exit;
                }
                break;
            case 'link_randkey':
                $newValue = rand(10000, 10000000);
                break;
            case 'link_url_title':
                $newValue = strip_tags($newValue);
                break;
            case 'link_status':
                if (!in_array($newValue, $this->whitelistLinkStatuses)) {
                    throw new \InvalidArgumentException(
                        "Attribute link_status can only be: " . implode(', ', $this->whitelistLinkStatuses) . "."
                    );
                    exit;
                }
                break;
        }
        return $newValue;
    }
    /**
     * Create a snippet summary of the given link content
     *
     * @param string $content The current content for the link
     * @return string The truncated summary
     * @access protected
     * @author Johnathan Pulos
     **/
    protected function createSummary($content)
    {
        $content = strip_tags($content);
        $contentLength = strlen($content);
        if ($contentLength > $this->summaryLength) {
            return substr($content, 0, $this->summaryLength);
        } else {
            return $content;
        }
    }
}
