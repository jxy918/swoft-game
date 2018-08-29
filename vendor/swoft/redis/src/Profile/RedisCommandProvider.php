<?php

namespace Swoft\Redis\Profile;

use Swoft\Bean\Annotation\Bean;
use Swoft\Bean\Annotation\Inject;
use Swoft\Redis\Operator\Processor\PrefixProcessor;

/**
 * Class RedisCommandProvider
 * @Bean()
 */
class RedisCommandProvider extends RedisProfile
{
    /**
     * @Inject()
     * @var PrefixProcessor
     */
    protected $processor;

    /**
     * @return array
     */
    public function getSupportedCommands(): array
    {
        return [

            /* connection related commands */
            'PING'                => '\Swoft\Redis\Operator\ConnectionPing',
            'SELECT'              => '\Swoft\Redis\Operator\ConnectionSelect',

            /* commands operating on the key space */
            /* ---------------- Redis 1.2 ---------------- */
            'EXISTS'              => '\Swoft\Redis\Operator\Keys\KeyExists',
            'DEL'                 => '\Swoft\Redis\Operator\Keys\KeyDelete',
            'TYPE'                => '\Swoft\Redis\Operator\Keys\KeyType',
            'KEYS'                => '\Swoft\Redis\Operator\Keys\KeyGetKeys',
            'GETKEYS'             => '\Swoft\Redis\Operator\Keys\KeyGetKeys',
            'RANDOMKEY'           => '\Swoft\Redis\Operator\Keys\KeyRandom',
            'RENAME'              => '\Swoft\Redis\Operator\Keys\KeyRenameKey',
            'RENAMEKEY'           => '\Swoft\Redis\Operator\Keys\KeyRenameKey',
            'RENAMENX'            => '\Swoft\Redis\Operator\Keys\KeyRenamePreserve',
            'EXPIREAT'            => '\Swoft\Redis\Operator\Keys\KeyExpireAt',
            'TTL'                 => '\Swoft\Redis\Operator\Keys\KeyTimeToLive',
            'MOVE'                => '\Swoft\Redis\Operator\Keys\KeyMove',
            'DUMP'                => '\Swoft\Redis\Operator\Keys\KeyDump',
            'RESTORE'             => '\Swoft\Redis\Operator\Keys\KeyRestore',
            /* ---------------- Redis 2.2 ---------------- */
            'PERSIST'             => '\Swoft\Redis\Operator\Keys\KeyPersist',
            /* ---------------- Redis 2.6 ---------------- */
            'PTTL'                => '\Swoft\Redis\Operator\Keys\KeyPreciseTimeToLive',
            'PEXPIRE'             => '\Swoft\Redis\Operator\Keys\KeyPreciseExpire',
            'PEXPIREAT'           => '\Swoft\Redis\Operator\Keys\KeyPreciseExpireAt',

            /* commands operating on string values */
            /* ---------------- Redis 1.2 ---------------- */
            'SET'                 => '\Swoft\Redis\Operator\Strings\StringSet',
            'SETNX'               => '\Swoft\Redis\Operator\Strings\StringSetPreserve',
            'MSET'                => '\Swoft\Redis\Operator\Strings\StringSetMultiple',
            'MSETNX'              => '\Swoft\Redis\Operator\Strings\StringSetMultiplePreserve',
            'GET'                 => '\Swoft\Redis\Operator\Strings\StringGet',
            'MGET'                => '\Swoft\Redis\Operator\Strings\StringGetMultiple',
            'GETSET'              => '\Swoft\Redis\Operator\Strings\StringGetSet',
            'INCR'                => '\Swoft\Redis\Operator\Strings\StringIncrement',
            'INCRBY'              => '\Swoft\Redis\Operator\Strings\StringIncrementBy',
            'DECR'                => '\Swoft\Redis\Operator\Strings\StringDecrement',
            'DECRBY'              => '\Swoft\Redis\Operator\Strings\StringDecrementBy',
            /* ---------------- Redis 2.0 ---------------- */
            'SETEX'               => '\Swoft\Redis\Operator\Strings\StringSetExpire',
            'APPEND'              => '\Swoft\Redis\Operator\Strings\StringAppend',
            /* ---------------- Redis 2.2 ---------------- */
            'STRLEN'              => '\Swoft\Redis\Operator\Strings\StringStrlen',
            'SETRANGE'            => '\Swoft\Redis\Operator\Strings\StringSetRange',
            'GETRANGE'            => '\Swoft\Redis\Operator\Strings\StringGetRange',
            'SETBIT'              => '\Swoft\Redis\Operator\Strings\StringSetBit',
            'GETBIT'              => '\Swoft\Redis\Operator\Strings\StringGetBit',
            /* ---------------- Redis 2.6 ---------------- */
            'PSETEX'              => '\Swoft\Redis\Operator\Strings\StringPreciseSetExpire',
            'INCRBYFLOAT'         => '\Swoft\Redis\Operator\Strings\StringIncrementByFloat',
            'BITOP'               => '\Swoft\Redis\Operator\Strings\StringBitOp',
            'BITCOUNT'            => '\Swoft\Redis\Operator\Strings\StringBitCount',

            /* commands operating on lists */
            /* ---------------- Redis 1.2 ---------------- */
            'RPUSH'               => '\Swoft\Redis\Operator\Lists\ListPushTail',
            'LPUSH'               => '\Swoft\Redis\Operator\Lists\ListPushHead',
            'LLEN'                => '\Swoft\Redis\Operator\Lists\ListLength',
            'LRANGE'              => '\Swoft\Redis\Operator\Lists\ListRange',
            'LGETRANGE'           => '\Swoft\Redis\Operator\Lists\ListRange',
            'LTRIM'               => '\Swoft\Redis\Operator\Lists\ListTrim',
            'LISTTRIM'            => '\Swoft\Redis\Operator\Lists\ListTrim',
            'LINDEX'              => '\Swoft\Redis\Operator\Lists\ListIndex',
            'LGET'                => '\Swoft\Redis\Operator\Lists\ListIndex',
            'LSET'                => '\Swoft\Redis\Operator\Lists\ListSet',
            'LREM'                => '\Swoft\Redis\Operator\Lists\ListRemove',
            'LPOP'                => '\Swoft\Redis\Operator\Lists\ListPopFirst',
            'RPOP'                => '\Swoft\Redis\Operator\Lists\ListPopLast',
            'RPOPLPUSH'           => '\Swoft\Redis\Operator\Lists\ListPopLastPushHead',
            /* ---------------- Redis 2.0 ---------------- */
            'BLPOP'               => '\Swoft\Redis\Operator\Lists\ListPopFirstBlocking',
            'BRPOP'               => '\Swoft\Redis\Operator\Lists\ListPopLastBlocking',
            /* ---------------- Redis 2.2 ---------------- */
            'RPUSHX'              => '\Swoft\Redis\Operator\Lists\ListPushTailX',
            'LPUSHX'              => '\Swoft\Redis\Operator\Lists\ListPushHeadX',
            'LINSERT'             => '\Swoft\Redis\Operator\Lists\ListInsert',
            'BRPOPLPUSH'          => '\Swoft\Redis\Operator\Lists\ListPopLastPushHeadBlocking',

            /* commands operating on sets */
            /* ---------------- Redis 1.2 ---------------- */
            'SADD'                => '\Swoft\Redis\Operator\Sets\SetAdd',
            'SREM'                => '\Swoft\Redis\Operator\Sets\SetRemove',
            'SREMOVE'             => '\Swoft\Redis\Operator\Sets\SetRemove',
            'SPOP'                => '\Swoft\Redis\Operator\Sets\SetPop',
            'SMOVE'               => '\Swoft\Redis\Operator\Sets\SetMove',
            'SCARD'               => '\Swoft\Redis\Operator\Sets\SetCardinality',
            'SSIZE'               => '\Swoft\Redis\Operator\Sets\SetCardinality',
            'SISMEMBER'           => '\Swoft\Redis\Operator\Sets\SetIsMember',
            'SCONTAINS'           => '\Swoft\Redis\Operator\Sets\SetIsMember',
            'SINTER'              => '\Swoft\Redis\Operator\Sets\SetIntersection',
            'SINTERSTORE'         => '\Swoft\Redis\Operator\Sets\SetIntersectionStore',
            'SUNION'              => '\Swoft\Redis\Operator\Sets\SetUnion',
            'SUNIONSTORE'         => '\Swoft\Redis\Operator\Sets\SetUnionStore',
            'SDIFF'               => '\Swoft\Redis\Operator\Sets\SetDifference',
            'SDIFFSTORE'          => '\Swoft\Redis\Operator\Sets\SetDifferenceStore',
            'SMEMBERS'            => '\Swoft\Redis\Operator\Sets\SetMembers',
            'SRANDMEMBER'         => '\Swoft\Redis\Operator\Sets\SetRandomMember',

            /* commands operating on sorted sets */
            /* ---------------- Redis 1.2 ---------------- */
            'ZADD'                => '\Swoft\Redis\Operator\ZSets\ZSetAdd',
            'ZINCRBY'             => '\Swoft\Redis\Operator\ZSets\ZSetIncrementBy',
            'ZREM'                => '\Swoft\Redis\Operator\ZSets\ZSetRemove',
            'ZDELETE'             => '\Swoft\Redis\Operator\ZSets\ZSetRemove',
            'ZRANGE'              => '\Swoft\Redis\Operator\ZSets\ZSetRange',
            'ZREVRANGE'           => '\Swoft\Redis\Operator\ZSets\ZSetReverseRange',
            'ZRANGEBYSCORE'       => '\Swoft\Redis\Operator\ZSets\ZSetRangeByScore',
            'ZCARD'               => '\Swoft\Redis\Operator\ZSets\ZSetCardinality',
            'ZSCORE'              => '\Swoft\Redis\Operator\ZSets\ZSetScore',
            'ZREMRANGEBYSCORE'    => '\Swoft\Redis\Operator\ZSets\ZSetRemoveRangeByScore',
            'ZDELETERANGEBYSCORE' => '\Swoft\Redis\Operator\ZSets\ZSetRemoveRangeByScore',
            /* ---------------- Redis 2.0 ---------------- */
            'ZCOUNT'              => '\Swoft\Redis\Operator\ZSets\ZSetCount',
            'ZRANK'               => '\Swoft\Redis\Operator\ZSets\ZSetRank',
            'ZREVRANK'            => '\Swoft\Redis\Operator\ZSets\ZSetReverseRank',
            'ZREMRANGEBYRANK'     => '\Swoft\Redis\Operator\ZSets\ZSetRemoveRangeByRank',
            'ZDELETERANGEBYRANK'  => '\Swoft\Redis\Operator\ZSets\ZSetRemoveRangeByRank',
            /* ---------------- Redis 2.2 ---------------- */
            'ZREVRANGEBYSCORE'    => '\Swoft\Redis\Operator\ZSets\ZSetReverseRangeByScore',
            /* ---------------- Redis 2.8 ---------------- */
            'ZRANGEBYLEX'         => '\Swoft\Redis\Operator\ZSets\ZSetRangeByLex',
            'ZREVRANGEBYLEX'      => '\Swoft\Redis\Operator\ZSets\ZSetReverseRangeByLex',

            /* commands operating on hashes */
            /* ---------------- Redis 1.2 ---------------- */
            'HSET'                => '\Swoft\Redis\Operator\Hashes\HashSet',
            'HSETNX'              => '\Swoft\Redis\Operator\Hashes\HashSetPreserve',
            'HMSET'               => '\Swoft\Redis\Operator\Hashes\HashSetMultiple',
            'HINCRBY'             => '\Swoft\Redis\Operator\Hashes\HashIncrementBy',
            'HGET'                => '\Swoft\Redis\Operator\Hashes\HashGet',
            'HMGET'               => '\Swoft\Redis\Operator\Hashes\HashGetMultiple',
            'HDEL'                => '\Swoft\Redis\Operator\Hashes\HashDelete',
            'HEXISTS'             => '\Swoft\Redis\Operator\Hashes\HashExists',
            'HLEN'                => '\Swoft\Redis\Operator\Hashes\HashLength',
            'HKEYS'               => '\Swoft\Redis\Operator\Hashes\HashKeys',
            'HVALS'               => '\Swoft\Redis\Operator\Hashes\HashValues',
            'HGETALL'             => '\Swoft\Redis\Operator\Hashes\HashGetAll',
            /* ---------------- Redis 2.6 ---------------- */
            'HINCRBYFLOAT'        => '\Swoft\Redis\Operator\Hashes\HashIncrByFloat',

            /* remote server control commands */
            /* ---------------- Redis 1.2 ---------------- */
            'DBSIZE'              => '\Swoft\Redis\Operator\Servers\ServerDatabaseSize',
            'FLUSHDB'             => '\Swoft\Redis\Operator\Servers\ServerFlushDatabase',
            'FLUSHALL'            => '\Swoft\Redis\Operator\Servers\ServerFlushAll',
            'SAVE'                => '\Swoft\Redis\Operator\Servers\ServerSave',
            'BGSAVE'              => '\Swoft\Redis\Operator\Servers\ServerBackgroundSave',
            'LASTSAVE'            => '\Swoft\Redis\Operator\Servers\ServerLastSave',
            'BGREWRITEAOF'        => '\Swoft\Redis\Operator\Servers\ServerBackgroundRewriteAOF',
            /* ---------------- Redis 2.6 ---------------- */
            'TIME'                => '\Swoft\Redis\Operator\Servers\ServerTime',
            'EVAL'                => '\Swoft\Redis\Operator\Servers\ServerEval',
            'EVALSHA'             => '\Swoft\Redis\Operator\Servers\ServerEvalSHA',

            /* remote transaction commands */
            /* ---------------- Redis 1.2 ---------------- */
            'EXEC'                => '\Swoft\Redis\Operator\Transactions\TransExec',
            'MULTI'               => '\Swoft\Redis\Operator\Transactions\TransMulti',
            'WATCH'               => '\Swoft\Redis\Operator\Transactions\TransWatch',
            'UNWATCH'             => '\Swoft\Redis\Operator\Transactions\TransUnWatch',

            /* remote pub/sub commands */
            /* ---------------- Redis 2.0 ---------------- */
            'PUBLISH'             => '\Swoft\Redis\Operator\PubSubs\PubSubPublish',
            'SUBSCRIBE'           => '\Swoft\Redis\Operator\PubSubs\PubSubSubscribe',
            'PSUBSCRIBE'          => '\Swoft\Redis\Operator\PubSubs\PubSubPSubscribe',
        ];
    }
}
