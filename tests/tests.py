import json
import webclient
import unittest
import mysql.connector

class TestBase(unittest.TestCase):

  def setUp(self):
    self.wc = webclient.WebClient('http://vogen.local/', 'admin', 'vogen');

    self.connection = mysql.connector.connect(host='localhost', user='voucher', password='voucher', database='voucher')

    cursor = self.connection.cursor(prepared=True)
    cursor.execute("show tables")

    tables = [row[0].decode('utf-8') for row in cursor]

    for tn in tables:
      cursor.execute('drop table `%s`' % (tn,))


    cursor.close()
    self.connection.commit()

  def tearDown(self):
    self.wc.disconnect()


class TestSms(TestBase):

  def setUp(self):
    TestBase.setUp(self)
    config = [{"label" : "Default", "table" : "sms", "countryPrefix" : "+41", "example" : "079 123 45 67", "text" : "Ticket: {TICKET}", "validator" : "0[0-9]{9}", "httpGet" : "http://vogen.local/testSmsProvider.php?text={TEXT}&number={NUMBER}"}]
    self.wc.updateSettings(sms_gateway = json.dumps(config));


  def testSend(self):
    self.wc.importTickets('sms', ['aaaaaa'])
    result = self.wc.sendSms('079 123 45 67')
    messages = self.wc.getSendtSms()
    
    self.assertIsNotNone(result.find("div", {'message-id': 'sendt-sms'}))

    self.assertEqual(len(messages), 1)
    self.assertEqual(messages[0].number, '+41791234567')
    
  def testSendWithNoTickets(self):
    result = self.wc.sendSms('079 123 45 67')
    messages = self.wc.getSendtSms()
    self.assertIsNotNone(result.find("div", {'message-id': 'no-unused-tickets'}))
    self.assertEqual(len(messages), 0)
    
  def testSendWithInvalidNumber(self):
    result = self.wc.sendSms('079 123 45 6')
    messages = self.wc.getSendtSms()
    self.assertIsNotNone(result.find("div", {'message-id': 'invalid-mobile-number'}))
    self.assertEqual(len(messages), 0)
    
  def testSendWithLockedNumber(self):
    self.wc.importTickets('sms', ['aaaaaa', 'bbbbbb'])
    self.wc.sendSms('079 123 45 67')
    result = self.wc.sendSms('079 123 45 67')

    self.assertIsNotNone(result.find("div", {'message-id': 'number-is-blocked'}))
    
  def testSendWithGatewayError(self):
    self.wc.importTickets('sms', ['aaaaaa', 'bbbbbb'])
    result = self.wc.sendSms('079 123 45 99')

    self.assertIsNotNone(result.find("div", {'message-id': 'gateway-error'}))
    
  def testIfSendtSmsIsLocked(self):
    self.wc.importTickets('sms', ['aaaaaa'])
    self.assertFalse(self.wc.isLocked('079 123 45 67'))
    result = self.wc.sendSms('079 123 45 67')
    self.assertTrue(self.wc.isLocked('079 123 45 67'))
    
    
#
# same as test sms but using captivate portal to send sms
#
class TestCPSms(TestSms):
  def setUp(self):
    TestSms.setUp(self)
    self.wc.useCP()

class TestDatabases(TestBase):

  def testCreate(self):
    self.wc.updateSettings(dbtables="aaa|AAA")
    tables = self.wc.getTablesFromStats()
    self.assertEqual(len(tables), 1)
    self.assertEqual(tables[0], 'aaa')

  def testCreateMultiple(self):
    self.wc.updateSettings(dbtables="aaa|AAA\nbbb|BBB\nccc|CCC")
    tables = self.wc.getTablesFromStats()
    self.assertEqual(len(tables), 3)
    self.assertEqual(tables[0], 'aaa')
    self.assertEqual(tables[1], 'bbb')
    self.assertEqual(tables[2], 'ccc')

  def testImport(self):
    self.wc.importTickets('default', ['aaaaaa', 'bbbbbb', 'cccccc', 'dddddd'])
    stats = self.wc.getStats('default')
    self.assertEqual(stats['total'], 4)
    self.assertEqual(stats['used'], 0)
    self.assertEqual(stats['unused'], 4)
    
  def testImportEmptyFile(self):
    # can not be tested properly, can't reconstruct exact post
    soup = self.wc.importTickets('default', "")
    self.assertIsNotNone(soup.find("div", {"message-id": "invalid-csv-export-message"}))

  def testImportInvalidFile(self):
    soup = self.wc.importTickets('default', "this\nis\nno\nvalid\ncvs\nfile\n\sdsa\nfoo\nbar\nend")
    self.assertIsNotNone(soup.find("div", {"message-id": "invalid-csv-export-message"}))

  def testPrint(self):
    self.wc.importTickets('default', ['aaaaaa', 'bbbbbb', 'cccccc', 'dddddd'])
    self.wc.printPDF('default', 2)
    stats = self.wc.getStats('default')
    self.assertEqual(stats['total'], 4)
    self.assertEqual(stats['used'], 2)
    self.assertEqual(stats['unused'], 2)


class TestConfig(TestBase):

  def testSimpleConfigs(self):
    self.wc.updateSettings(vou_header='HEADER', vou_text='TEXT', vou_label='LABEL')
    settings = self.wc.getSettings()

    self.assertEqual(settings['vou_header'], 'HEADER')
    self.assertEqual(settings['vou_text'], 'TEXT')
    self.assertEqual(settings['vou_label'], 'LABEL')

    self.wc.updateSettings(vou_header='FOO', vou_text='Bar', vou_label='foooooBar')
    settings = self.wc.getSettings()

    self.assertEqual(settings['vou_header'], 'FOO')
    self.assertEqual(settings['vou_text'], 'Bar')
    self.assertEqual(settings['vou_label'], 'foooooBar')



if __name__ == '__main__':
  unittest.main()
