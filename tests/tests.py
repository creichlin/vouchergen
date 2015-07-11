import webclient
import unittest


class TestConfig(unittest.TestCase):

  def setUp(self):
    self.wc = webclient.WebClient('http://vogen.local/', 'admin', 'vogen');
    
  def tearDown(self):
    self.wc.disconnect()


  def testSimpleConfigs(self):
    self.wc.updateSettings(vou_header = 'HEADER', vou_text = 'TEXT', vou_label = 'LABEL')
    settings = self.wc.getSettings()
    
    self.assertEqual(settings['vou_header'], 'HEADER')
    self.assertEqual(settings['vou_text'], 'TEXT')
    self.assertEqual(settings['vou_label'], 'LABEL')
    
    self.wc.updateSettings(vou_header = 'FOO', vou_text = 'Bar', vou_label = 'foooooBar')
    settings = self.wc.getSettings()
    
    self.assertEqual(settings['vou_header'], 'FOO')
    self.assertEqual(settings['vou_text'], 'Bar')
    self.assertEqual(settings['vou_label'], 'foooooBar')
    
    
    
if __name__ == '__main__':
  unittest.main()