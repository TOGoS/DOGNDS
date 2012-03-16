module DOGNDS ; module Crypto
  module KeyUtil
    # Will return an ASN.1 structure representing the 'public key
    # info' object, which you can then .to_der, the result of which
    # should be acceptable to Java and PHP's OpenSSL public
    # key-loading functions (though in PHP's case you will still need
    # to convert it to a PEM).
    #
    # key should be a OpenSSL::PKey::RSA object
    def self.public_key_info( key )
      OpenSSL::ASN1::Sequence.new([
        OpenSSL::ASN1::Sequence.new([
          OpenSSL::ASN1::ObjectId.new('rsaEncryption'),
          OpenSSL::ASN1::Null.new(nil)
        ]),
        OpenSSL::ASN1::BitString.new(key.public_key.to_der)
      ])
    end
  end
end ; end
