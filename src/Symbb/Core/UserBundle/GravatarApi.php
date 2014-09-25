<?php
/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace SymBB\Core\UserBundle;

class GravatarApi
{
    /**
     * @var array $defaults Array of default options that can be overriden with getters and in the construct.
     */
    protected $defaults = array(
        'size'    => 80,
        'rating'  => 'g',
        'default' => null,
        'secure'  => false,
    );

    /**
     * Constructor
     *
     * @param array $options the array is merged with the defaults.
     * @return void
     */
    public function __construct(array $options = array())
    {
        $this->defaults = array_merge($this->defaults, $options);
    }

    /**
     * Returns a url for a gravatar.
     *
     * @param  string  $email
     * @param  integer $size
     * @param  string  $rating
     * @param  string  $default
     * @param  Boolean $secure
     * @return string
     */
    public function getUrl($email, $size = null, $rating = null, $default = null, $secure = null)
    {
        $hash = md5(strtolower(trim($email)));

        return $this->getUrlForHash($hash, $size, $rating, $default, $secure);
    }

    /**
     * Returns a url for a gravatar for the given hash.
     *
     * @param  string  $hash
     * @param  integer $size
     * @param  string  $rating
     * @param  string  $default
     * @param  Boolean $secure
     * @return string
     */
    public function getUrlForHash($hash, $size = null, $rating = null, $default = null, $secure = null)
    {
        $map = array(
            's' => $size    ?: $this->defaults['size'],
            'r' => $rating  ?: $this->defaults['rating'],
            'd' => $default ?: $this->defaults['default'],
        );

        if (null === $secure) {
            $secure = $this->defaults['secure'];
        }

        return ($secure ? 'https://secure' : 'http://www') . '.gravatar.com/avatar/' . $hash . '?' . http_build_query(array_filter($map));
    }

    /**
     * Checks if a gravatar exists for the email. It does this by checking for 404 Not Found in the
     * body returned.
     *
     * @param string $email
     * @return Boolean
     */
    public function exists($email)
    {
        $path       = $this->getUrl($email, null, null, '404');
        $errorNo    = null;
        $error      = null;
        $sock       = fsockopen('gravatar.com', 80, $errorNo, $error);
        fputs($sock, "HEAD " . $path . " HTTP/1.0\r\n\r\n");

        $header = fgets($sock, 128);

        fclose($sock);

        return strpos($header, '404') ? false : true;
    }
}