<?php

declare(strict_types=1);

namespace App\Model;

use App\Constants\EndpointsConstants as Endpoints;
use App\Curl\CurlException;
use App\Curl\CurlHandler;

/**
 * Provides all management for subjects
 *
 * @since 1.0.1
 * @package App\Model
 */
class SubjectManager
{

  private CurlHandler $curlHandler;

  /**
   * SubjectManager constructor
   *
   * @param CurlHandler $curlHandler
   */
  public function __construct(CurlHandler $curlHandler)
  {
    $this->curlHandler = $curlHandler;
  }

  /**
   * Adds new user to existing subject
   *
   * @param  int  $userId  Id of new user
   * @param  int  $subjectId Id of existing subject
   * @param  string  $role Role of new user
   * @throws CurlException
   */
  public function addUserToSubject(int $userId, int $subjectId, string $role): void
  {
    $data = ([
      'userId' => $userId,
      'role' => $role
    ]);
    $endPoint = Endpoints::SUBJECTS.'/'.$subjectId.Endpoints::USERS;
    $this->curlHandler->postData($endPoint, $data);
  }

  /**
   * Creates new subject
   *
   * @param array $data Information regarding the new subject
   *
   * @return array
   * @throws CurlException
   */
  public function addSubject(array $data): array
  {
    $endPoint = Endpoints::SUBJECTS;
    return $this->curlHandler->postData($endPoint, $data);
  }

  /**
   * Gets all existing subjects
   *
   * @return array All subjects
   */
  public function getSubjects(): array
  {
    return $this->curlHandler->getSubjects();
  }

  /**
   * Updates information regarding the subject
   *
   * @param int $id Id of subject
   * @param array $data Updated information regarding the subject
   *
   * @return array
   * @throws CurlException
   */
  public function updateSubject(int $id, array $data): array
  {
    $endPoint = Endpoints::SUBJECT.'/'.$id;
    return $this->curlHandler->putData($endPoint, $data);
  }

  /**
   * Gets subject by the specified id
   *
   * @param int $id Id of subject
   *
   * @return array Information regarding the subject
   */
  public function getSubjectById(int $id): array
  {
    return $this->curlHandler->getUserById($id);
  }
}
