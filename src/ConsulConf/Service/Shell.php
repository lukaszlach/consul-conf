<?php

namespace ConsulConf\Service;

class Shell
{
    /** @var User */
    protected $user;

    /**
     * @param string $command
     * @param array $parameters
     * @param array $env
     * @return string
     * @throws \Exception
     */
    public function exec($command, array $parameters = [], array $env = [])
    {
        $command = [$command];
        foreach ($parameters as $parameter) {
            $command[] = escapeshellarg($parameter);
        }
        $command = implode(' ', $command);
        $subCommands = [];
        foreach ($env as $variable) {
            $subCommands[] = 'export '.escapeshellarg($variable);
        }
        $subCommands[] = escapeshellcmd($command);
        $command = 'bash -c "'.implode('; ', $subCommands).'"';
        exec($command, $result, $exitCode);
        if (0 !== $exitCode) {
            throw new \Exception("Failed to execute $command (exit-code: $exitCode)");
        }
        $result = implode("\n", $result);

        return $result;
    }

    /**
     * @param string $command
     * @param array $parameters
     * @param array $env
     * @return string
     * @throws \Exception
     */
    public function execForUser($command, array $parameters = [], array $env = [])
    {
        $userConsulAclToken = $this->user->getConsulAclToken();
        if (null !== $userConsulAclToken) {
            // overwrite acl token even if set with configuration file
            $env[] = "CONSUL_ACL_TOKEN=$userConsulAclToken";
        }
        return $this->exec($command, $parameters, $env);
    }

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }
}