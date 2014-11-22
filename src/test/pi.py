py = """
class User(object):
    def __init__(self, name):
        self.name = name
    def __repr__(self):
        return "<User: {0:x}; name={1}>".format(id(self), self.name)
    """
ns =dict()
exec py in ns
print ns.keys()
print ns["User"]("Tom")
class User(object):
    def __init__(self, name):
        self.name = name
    def __repr__(self):
        return "<User: {0:x}; name={1}>".format(id(self), self.name)

print User('Tom')
name = '23'
print "<User: {0:x}; name={1}>".format(id(name),name)

