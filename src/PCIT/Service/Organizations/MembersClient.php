<?php

declare(strict_types=1);

namespace PCIT\Service\GitHub\Organizations;

use PCIT\Service\CICommon;

class MembersClient
{
    use CICommon;

    /**
     * Members list.
     *
     * @param string $org_name org name
     * @param string $filter   2fa_disabled or all
     * @param string $role     admin or member or all
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function list(string $org_name, string $filter = 'all', string $role = 'all')
    {
        return $this->curl->get($this->api_url.'/orgs/'.$org_name.'/members?'.http_build_query([
                'filter' => $filter,
                'role' => $role,
            ]));
    }

    /**
     * Check membership.
     *
     * @param string $org_name
     * @param string $username
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function checkMembership(string $org_name, string $username)
    {
        $this->curl->get($this->api_url.'/orgs/'.$org_name.'/members/'.$username);

        $http_return_code = $this->curl->getCode();

        if ('204' === $http_return_code) {
            return true;
        }

        return false;
    }

    /**
     * Remove a member.
     *
     * 204
     *
     * @param string $org_name
     * @param string $username
     *
     * @throws \Exception
     */
    public function remove(string $org_name, string $username): void
    {
        $this->curl->delete($this->api_url.'/orgs/'.$org_name.'/members/'.$username);
    }

    /**
     * Public members list.
     *
     * @param string $org_name
     *
     * @throws \Exception
     */
    public function listPublic(string $org_name): void
    {
        $this->curl->get($this->api_url.'/orgs/'.$org_name.'/public_members');
    }

    /**
     * Check public membership.
     *
     * 204
     *
     * @param string $org_name
     * @param string $username
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function checkPublicMembership(string $org_name, string $username)
    {
        $this->curl->get($this->api_url.'/orgs/'.$org_name.'/public_members/'.$username);

        $http_return_code = $this->curl->getCode();

        if ('204' === $http_return_code) {
            return true;
        }

        return false;
    }

    /**
     * Publicize a user's membership.
     *
     * 204
     *
     * @param string $org_name
     * @param string $username
     *
     * @throws \Exception
     */
    public function publicizeUserMembership(string $org_name, string $username): void
    {
        $this->curl->put($this->api_url.'/orgs/'.$org_name.'/public_members/'.$username);
    }

    /**
     * Conceal a user's membership.
     *
     * @param string $org_name
     * @param string $username
     *
     * @throws \Exception
     */
    public function concealUserMembership(string $org_name, string $username): void
    {
        $this->curl->delete($this->api_url.'/orgs/'.$org_name.'/public_members/'.$username);
    }

    /**
     * Get organization membership.
     *
     * @param string $org_name
     * @param string $username
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function getMembership(string $org_name, string $username)
    {
        return $this->curl->get($this->api_url.'/orgs/'.$org_name.'/memberships/'.$username);
    }

    /**
     * Add or update organization membership.
     *
     * @param string $org_name
     * @param string $username username
     * @param string $role     admin or member
     *
     * @throws \Exception
     */
    public function updateMembership(string $org_name, string $username, string $role = 'member'): void
    {
        $this->curl->put($this->api_url.'/orgs/'.$org_name.'/memberships/'.$username.'?'.http_build_query([
                'role' => $role,
            ]));
    }

    /**
     * Remove organization membership.
     *
     * 204
     *
     * @param string $org_name
     * @param string $username
     *
     * @throws \Exception
     */
    public function removeMembership(string $org_name, string $username): void
    {
        $this->curl->delete($this->api_url.'/orgs/'.$org_name.'/memberships/'.$username);
    }

    /**
     * List organization invitation teams.
     *
     * @param string $org_name
     * @param string $invitation_id
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function listInvitationTeams(string $org_name, string $invitation_id)
    {
        return $this->curl->get($this->api_url.'/orgs/'.$org_name.'/invitations/'.$invitation_id.'/teams');
    }

    /**
     * List pending organization invitations.
     *
     * @param string $org_name
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function listPendingInvitations(string $org_name)
    {
        return $this->curl->get($this->api_url.'/orgs/'.$org_name.'/invitations');
    }

    /**
     * Create organization invitation.
     *
     * 201
     *
     * @param string   $org_name
     * @param int|null $invitee_id GotHub user id
     * @param string   $email
     * @param string   $role       admin or direct_member billing_manager
     * @param array    $team_ids
     *
     * @throws \Exception
     */
    public function createInvitation(string $org_name,
                                     ?int $invitee_id,
                                     ?string $email,
                                     string $role = 'direct_member',
                                     array $team_ids = null): void
    {
        $data = [
            'invitee_id' => $invitee_id,
            'email' => $email,
            'role' => $role,
            'team_ids' => $team_ids,
        ];

        $this->curl->post($this->api_url.'/orgs/'.$org_name.'/invitations', json_encode(array_filter($data)));
    }

    /**
     * List your organization memberships.
     *
     * @param string $state active or pending
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function listYourMemberships(string $state = null)
    {
        return $this->curl->get($this->api_url.'/user/memberships/orgs?'.http_build_query([
                    'state' => $state,
                ]
            ));
    }

    /**
     * Get your organization membership.
     *
     * @param string $org_name
     *
     * @throws \Exception
     */
    public function getYourMembership(string $org_name): void
    {
        $this->curl->get($this->api_url.'/user/memberships/orgs/'.$org_name);
    }

    /**
     * Edit your organization membership.
     *
     * @param string $org_name
     *
     * @throws \Exception
     */
    public function editYourMembership(string $org_name): void
    {
        $this->curl->patch($this->api_url.'/user/memberships/orgs/'.$org_name, [
            'state' => 'active',
        ]);
    }

    /**
     * List outside collaborators.
     *
     * @param string $org_name org name
     * @param string $filter   all or 2fa_disabled
     *
     * @throws \Exception
     */
    public function listOutsideCollaborators(string $org_name, string $filter = 'all'): void
    {
        $this->curl->get($this->api_url.'/orgs/'.$org_name.'/outside_collaborators?'.http_build_query([
                    'filter' => $filter,
                ]
            ));
    }

    /**
     * Remove outside collaborator.
     *
     * 204
     *
     * @param string $org_name
     * @param string $username
     *
     * @throws \Exception
     */
    public function removeOutsideCollaborators(string $org_name, string $username): void
    {
        $this->curl->delete($this->api_url.'/orgs/'.$org_name.'/outside_collaborators/'.$username);
    }

    /**
     * Convert member to outside collaborator.
     *
     * 204
     *
     * @param string $org_name
     * @param string $username
     *
     * @throws \Exception
     *
     * @see https://developer.github.com/v3/orgs/outside_collaborators/#convert-member-to-outside-collaborator
     */
    public function ConvertMemberToOutsideCollaborator(string $org_name, string $username): void
    {
        $this->curl->put($this->api_url.'/orgs/'.$org_name.'/outside_collaborators/'.$username);
    }
}
