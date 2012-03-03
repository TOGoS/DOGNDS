module TOGoS

  # Encodes and decodes strings to/from RFC-3548 Base32 (see http://tools.ietf.org/html/rfc3548)
  # Based on code found at http://bitcollider.cvs.sourceforge.net/bitcollider/jbitcollider/plugins/org.bitpedia.collider.core/src/org/bitpedia/util/Base32.java?view=markup
  module Base32
    BASE32_CHARSET = [
    	?A, ?B, ?C, ?D, ?E, ?F, ?G, ?H, ?I, ?J, ?K, ?L, ?M, ?N, ?O, ?P,
    	?Q, ?R, ?S, ?T, ?U, ?V, ?W, ?X, ?Y, ?Z, ?2, ?3, ?4, ?5, ?6, ?7
    ]
    BASE32_LOOKUP = [
      nil ,nil ,0x1A,0x1B,0x1C,0x1D,0x1E,0x1F, # '0', '1', '2', '3', '4', '5', '6', '7'
      nil ,nil ,nil ,nil ,nil ,nil ,nil ,nil , # '8', '9', ':', ';', '<', '=', '>', '?'
      nil ,0x00,0x01,0x02,0x03,0x04,0x05,0x06, # '@', 'A', 'B', 'C', 'D', 'E', 'F', 'G'
      0x07,0x08,0x09,0x0A,0x0B,0x0C,0x0D,0x0E, # 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O'
      0x0F,0x10,0x11,0x12,0x13,0x14,0x15,0x16, # 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W'
      0x17,0x18,0x19,nil ,nil ,nil ,nil ,nil , # 'X', 'Y', 'Z', '[', '\', ']', '^', '_'
      nil ,0x00,0x01,0x02,0x03,0x04,0x05,0x06, # '`', 'a', 'b', 'c', 'd', 'e', 'f', 'g'
      0x07,0x08,0x09,0x0A,0x0B,0x0C,0x0D,0x0E, # 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o'
      0x0F,0x10,0x11,0x12,0x13,0x14,0x15,0x16, # 'p', 'q', 'r', 's', 't', 'u', 'v', 'w'
      0x17,0x18,0x19,nil ,nil ,nil ,nil ,nil   # 'x', 'y', 'z', '{', '|', '}', '~', 'DEL'
    ]
  
    def self.encode32( normalstring )
      base32string = ""
      nsl = normalstring.length
      i = 0
      index = 0
      digit = 0
      
      while i < nsl
        curr_byte = normalstring[i]
      
        if index > 3
          if i + 1 < nsl
            next_byte = normalstring[i+1]
          else
            next_byte = 0
          end
          
          digit = curr_byte & (0xFF >> index)
          index = (index + 5) % 8
          digit <<= index
          digit |= (next_byte >> (8 - index))
          i += 1
        else
          digit = (curr_byte >> (8 - (index + 5))) & 0x1F
          index = (index + 5) % 8
          i += 1 if index == 0
        end
        base32string << BASE32_CHARSET[digit]
      end
      
      return base32string
    end
    
    def self.decode32( base32string )
      b32l = base32string.length
      nsl = (b32l * 5 / 8)
      normalstring = "\0" * nsl
      i = 0
      index = 0
      offset = 0
      
      while i < b32l
        lookup = base32string[i] - ?0
        next if lookup < 0
        digit = BASE32_LOOKUP[lookup]

        if index <= 3
          index = (index + 5) % 8
          if index == 0
            normalstring[offset] |= digit
            offset += 1
            break if offset > nsl
          else
            normalstring[offset] |= (digit << (8 - index))
          end
        else
          index = (index + 5) % 8
          normalstring[offset] |= (digit >> index)
          offset += 1
          break if offset >= nsl
          normalstring[offset] |= (digit << (8 - index))
        end

        i += 1
      end
      
      return normalstring
    end
  end
end