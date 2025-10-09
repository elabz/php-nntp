<?php

namespace Rvdv\Nntp\Command;

use Rvdv\Nntp\Exception\RuntimeException;
use Rvdv\Nntp\Response\Response;

/**
 * StatCommand.
 *
 * @author Cascade
 */
class StatCommand extends Command implements CommandInterface
{
    /**
     * @var string
     */
    private $article;

    /**
     * Constructor.
     *
     * @param string $article The message-id of the article
     */
    public function __construct($article)
    {
        $this->article = $article;
        parent::__construct(false);
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke()
    {
        return sprintf('STAT %s', $this->article);
    }

    /**
     * @return array
     */
    public function onArticleRetrieved(Response $response)
    {
        // Response: 223 12345 <message-id> article retrieved
        $parts = explode(' ', $response->getMessage());

        return [
            'number' => isset($parts[0]) ? (int) $parts[0] : null,
            'message_id' => isset($parts[1]) ? $parts[1] : null,
        ];
    }

    public function onNoNewsGroupCurrentSelected(Response $response)
    {
        throw new RuntimeException('A group must be selected first before getting an article status.', $response->getStatusCode());
    }

    public function onNoSuchArticleNumber(Response $response)
    {
        throw new RuntimeException('No article with that number.', $response->getStatusCode());
    }

    public function onNoSuchArticleId(Response $response)
    {
        throw new RuntimeException('No article with that message-id.', $response->getStatusCode());
    }
}
