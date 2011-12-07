
class SetDict (dict) :
    """Dictionary of Sets"""
    def insert(self,key,val) :
        """Add an item to the set stored at key"""
        if not self.has_key(key) :
            self[key] = set([])
        self[key].add(val)
            
    def contains(self,x,y=None) :
        """ If two arguments are given, see if item y is in the set stored at x. 
        If only one argument, x, is given, see if it occurs in any of the sets 
        """
        if not (y is None) :
            return y in self[x]
        for k in self.keys() :
            if x in self[k] :
                return True
        return False

    def pp(self) :
        "Pretty Print"
        for key in self.iterkeys() :
            print "\n:",key
            for x in self[key] :
                print x,
        
       
