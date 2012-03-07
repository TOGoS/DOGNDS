require 'socket'

module DOGNDS ; module HTTP
  class HTTPUtil
    def self.normalize_headers( headers )
      normalized_headers = {}
      for (k,v) in headers
        k = k.downcase
        normalized_headers[k] ||= []
        if v.is_a? Array
          for v in v ; normalized_headers[k] << v ; end
        else
          normalized_headers[k] << v
        end
      end
      return normalized_headers
    end
    
    def self.header_lines( headers )
      header_lines = []
      for (k,v) in headers
        if v.is_a? Array
          for v in v
            header_lines << "#{k}: #{v}"
          end
        else
          header_lines << "#{k}: #{v}"
        end
      end
      return header_lines
    end
  end
  
  class HTTPRequest
    attr_accessor :verb, :resource_name
    attr_accessor :http_headers
    attr_accessor :content
    
    def initialize( verb, resource_name, headers={}, content=nil )
      self.verb = verb
      self.resource_name = resource_name
      self.http_headers = headers
      self.content = content
    end
  end
  
  class HTTPResponse
    attr_accessor :status_code, :status_text
    attr_accessor :http_headers
    attr_accessor :content
    
    def initialize
      self.http_headers = {}
    end
  end
  
  class SimpleHTTP1_0Client
    def format_request( req )
      if req.resource_name =~ %r<http://(?:([^/]+)@)?([^/]*)>
        auth = $1
        host = $2
        path = $'
      else
        raise "Not recognising this as an HTTP URL: "+req.resource_name
      end
      
      headers = HTTPUtil.normalize_headers( req.http_headers )
      headers['host'] = host
      
      content_allowed = req.verb != 'GET' && req.verb != 'HEAD'
      if content_allowed and req.content
        content = req.content.to_s
        headers['content-length'] = content.length
      else
        content = ''
        headers.delete('content-length')
      end
      
      header_lines = HTTPUtil.header_lines( headers )
      return "#{req.verb} #{path} HTTP/1.0\r\n" +
        header_lines.join("\r\n") +
        (header_lines.length == 0 ? "\r\n" : "\r\n\r\n") +
        content
    end
    
    def call( req )
      if req.resource_name =~ %r<http://(?:([^/]+)@)?([^/]+)>
        auth = $1
        host = $2
        if host =~ /:(\d+)$/
          host = $`
          port = $1.to_i
        else
          port = 80
        end
        if host =~ /^\[([^\]]+)\]$/
          host = $1
        end
      else
        raise "Not recognising this as an HTTP URL: "+req.resource_name
      end
      
      TCPSocket.open( host, port ) do |sock|
        sock.write format_request(req)
        sock.flush
        
        status_line = sock.gets.strip
        res = HTTPResponse.new
        (prot,status_code,status_text) = status_line.split(" ",3)
        res.status_code = status_code.to_i
        res.status_text = status_text
        while line = sock.gets and line.strip! and line != ''
          (k,v) = line.split(/:\s+/,2)
          (res.http_headers[k.downcase] ||= []) << v
        end
        res.content = sock.read
        return res
      end
    end
  end
end ; end
