<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Resource\CloudAccount;

use GuzzleHttp\Promise\Promise;

use Nexcess\Sdk\ {
  Resource\App\Entity as App,
  Resource\CloudAccount\CloudAccountException,
  Resource\CloudAccount\Entity as CloudAccount,
  Resource\Model,
  Resource\Modelable,
  Util\Util
};

/**
 * Backup Entity
 */
class Backup extends Model {

  /** {@inheritDoc} */
  public const MODULE_NAME = 'CloudAccount';

  /** {@inheritDoc} */
  protected const _PROPERTY_ALIASES = [];

  /** {@inheritDoc} */
  protected const _PROPERTY_COLLAPSED = [];

  /** {@inheritDoc} */
  protected const _PROPERTY_MODELS = [];

  /** {@inheritDoc} */
  protected const _PROPERTY_NAMES = [];

  /** {@inheritDoc} */
  protected const _READONLY_NAMES = [
    'filepath',
    'filename',
    'filesize',
    'filesize_bytes',
    'type',
    'download_url',
    'filedate',
    'complete'
  ];

  /** @var CloudAccount The cloud account that "owns" this backup. */
  protected $_cloud_account;

  /**
   * Delete this backup
   *
   * @throws CloudAccountException
   */
  public function delete() : void {
    if (! $this->isReal()) {
      throw new CloudAccountException(
        CloudAccountException::INVALID_BACKUP,
        ['action' => __METHOD__]
      );
    }

    $this->_getEndpoint()->deleteBackup(
      $this->getCloudAccount(),
      $this->get('filename')
    );
  }

  /**
   * Download this backup
   *
   * @param string $path Where to save the file to
   * @throws CloudAccountException
   */
  public function download(string $path) : void {
    if (! $this->isReal()) {
      throw new CloudAccountException(
        CloudAccountException::INVALID_BACKUP,
        ['action' => __METHOD__]
      );
    }

    $this->_getEndpoint()->downloadBackup(
      $this->getCloudAccount(),
      $this->get('filename'),
      $path
    );
  }

  /**
   * Compare a Backup object to this one
   *
   * @param Modelable $other The other object to compare
   * @return bool true if the file names match
   */
  public function equals(Modelable $other) : bool {
    return ($other instanceof $this) &&
      $this->isReal() &&
      ($other->get('filename') === $this->get('filename'));
  }

  /**
   * Gets the cloud account that "owns" this backup.
   *
   * @return CloudAccount
   */
  public function getCloudAccount() : CloudAccount {
    if (empty($this->_cloud_account)) {
      throw new CloudAccountException(
        CloudAccountException::OWNER_UNKNOWN,
        ['filename' => $this->get('filename')]
      );
    }

    return $this->_cloud_account;
  }

  /**
   * Check to see if this is a complete object
   *
   * @return bool true if it has a non-empty file name
   */
  public function isReal() : bool {
    return ! empty($this->_values['filename']);
  }

  /**
   * Sets the cloud account that "owns" this backup.
   *
   * Note, this method is intended primarily for internal use by Endpoints.
   * It is important to be sure that this Backup actually "belongs"
   * to the given cloud account, or things will break.
   *
   * @param CloudAccount $cloud_account The "owner" cloud account
   * @return Backup $this
   */
  public function setCloudAccount(CloudAccount $cloud_account) : Backup {
    $this->_cloud_account = $cloud_account;
    return $this;
  }

  /**
   * Resolves when this Backup is complete.
   *
   * @param array $options Promise options
   * @return Promise Backup[complete] = true
   */
  public function whenComplete(array $options = []) : Promise {
    return $this->_getEndpoint()->whenBackupComplete($this, $options);
  }

  /**
   * {@inheritDoc}
   * Overridden to handle special retrieve case.
   */
  protected function _tryToHydrate() {
    if (
      $this->_hasEndpoint() &&
      isset($this->_cloud_account, $this->_values['filename']) &&
      ! $this->_hydrated
    ) {
      $model = $this->_getEndpoint()
        ->getBackup($this->getCloudAccount(), $this->get('filename'));
      $this->_values += $model->_values;
      foreach ($this->_values as $property => $value) {
        if (isset($value)) {
          continue;
        }

        $this->_values[$property] = $model->_values[$property];
      }

      $this->_hydrated = true;
    }
  }
}
