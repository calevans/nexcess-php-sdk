<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Resource\ApiToken;

use Nexcess\Sdk\ {
  Resource\ApiToken\ApiToken,
  Resource\CanCreate,
  Resource\CanDelete,
  Resource\CanUpdate,
  Resource\Creatable,
  Resource\Deletable,
  Resource\Endpoint as BaseEndpoint,
  Resource\Updatable,
  Util\Util
};

/**
 * API actions for portal Login.
 */
class Endpoint
  extends BaseEndpoint
  implements Creatable, Deletable, Updatable {
  use CanCreate, CanDelete, CanUpdate;

  /** {@inheritDoc} */
  public const MODULE_NAME = 'ApiToken';

  /** {@inheritDoc} */
  protected const _URI = 'api-token';

  /** {@inheritDoc} */
  protected const _MODEL_FQCN = ApiToken::class;

  /** {@inheritDoc} */
  protected const _PARAMS = [
    'create' => ['name' => [Util::TYPE_STRING]]
  ];
}
