<?php namespace System\Libraries\Auth;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\ValidationData;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Hmac\Sha256;

class Jwt{


    protected $ttl;

    protected $permittedFor;

    protected $identifiedBy;

    /**@var Builder*/
    protected $builder;

    /**@var Parser*/
    protected $parser;

    protected $signer;

    /**@var Token*/
    protected $token;

    protected $config;

    protected $claims = [];


    public function __construct()
    {
        $this->builder = new Builder();

        $this->parser  = new Parser();

        $this->signer = new Sha256();

        $this->config = config('jwt');
    }


    public function addClaim(string $key,$value): self
    {
        $this->claims[$key] = $value;

        return $this;
    }

    public function set(string $key,$value): self
    {
        return $this->addClaim($key,$value);
    }

    public function for(string $permittedFor): self
    {
        $this->permittedFor = $permittedFor;

        return $this;
    }


    public function setId($id): self
    {
        $this->identifiedBy = $id;

        return $this;
    }


    public function expire(int $seconds): self
    {
        $this->ttl = $seconds;

        return $this;
    }

    public function getToken(): Token
    {
        $time = time();

        $token = $this->builder->issuedBy($this->config['issued']);

        if($this->permittedFor !== null && is_string($this->permittedFor)) {
            $token->permittedFor($this->permittedFor);
        }

        if($this->identifiedBy !== null) {
            $token->identifiedBy($this->identifiedBy, true);
        }

        $token->issuedAt($time)->canOnlyBeUsedAfter($time + 60);

        $token->expiresAt($time + ($this->ttl ??$this->config['expires']));

        foreach(array_merge($this->config['claims'],$this->claims) as $key => $value) {
            $token->withClaim($key, $value);
        }

        return $this->token = $token->getToken($this->signer,new Key($this->config['key']));

    }


    public function parseToken(string $token): Token
    {
        return $this->parser->parse($token);
    }

    public function make(string $token): self
    {
        $this->token = $this->parser->parse($token);

        return $this;
    }

    public function get(string $key,$default =null)
    {
        if($this->token) {
            return $this->token->getClaim($key,$default);
        }
        return $default;
    }


    public function validate(): bool
    {
        $data = new ValidationData();

        $data->setIssuer($this->config['issued']);

        if($this->permittedFor !== null && is_string($this->permittedFor)) {
            $data->setAudience($this->permittedFor);
        }


        if($this->identifiedBy !== null) {
            $data->setId($this->identifiedBy);
        }

        $data->setCurrentTime(time() + 60);

        return $this->token->verify($this->signer, $this->config['key'])
            && $this->token->validate($data);
    }
}