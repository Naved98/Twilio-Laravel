import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;

void main() {
  runApp(const MyApp());
}

class MyApp extends StatelessWidget {
  const MyApp({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Flutter WhatsApp Template',
      theme: ThemeData(
        primarySwatch: Colors.blue,
      ),
      home: const SendMessageScreen(),
    );
  }
}

class SendMessageScreen extends StatefulWidget {
  const SendMessageScreen({Key? key}) : super(key: key);

  @override
  _SendMessageScreenState createState() => _SendMessageScreenState();
}

class _SendMessageScreenState extends State<SendMessageScreen> {
  final _formKey = GlobalKey<FormState>();
  final TextEditingController _phoneController = TextEditingController();
  final TextEditingController _nameController = TextEditingController();

  Future<void> sendWhatsAppTemplate(
      String recipientPhoneNumber, String customerName) async {
    final url = Uri.parse('http://192.168.1.76/twilio/msg.php'); // Update with the correct URL

    final Map<String, dynamic> data = {
      'to': recipientPhoneNumber,
      'customer_name': customerName,
      'request_id': 'REQ123', // Sample Request ID
      'service': 'Haircut',   // Sample Service
      'date': '2024-12-20',   // Sample Date
      'time': '10:00 AM',     // Sample Time
      'cost': '\$50',         // Sample Cost
      'salon': 'Beauty Salon',// Sample Salon Name
      'phone_number': '9876543210', // Sample Salon Phone
    };

    try {
      final response = await http.post(
        url,
        headers: {'Content-Type': 'application/json'},
        body: json.encode(data),
      );

      print('Raw Response: ${response.body}');
      print('Status Code: ${response.statusCode}');

      if (response.statusCode == 200) {
        final responseBody = json.decode(response.body);
        if (responseBody['success'] == true) {
          print('Message sent successfully! SID: ${responseBody['sid']}');
        } else {
          print('Error: ${responseBody['error']}');
        }
      } else {
        print('Failed to connect to the server');
      }
    } catch (e) {
      print('Error sending message: $e');
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Send WhatsApp Template')),
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Form(
          key: _formKey,
          child: Column(
            children: <Widget>[
              TextFormField(
                controller: _phoneController,
                decoration: const InputDecoration(labelText: 'Phone Number'),
                keyboardType: TextInputType.phone,
                validator: (value) {
                  if (value == null || value.isEmpty) {
                    return 'Please enter the phone number';
                  }
                  return null;
                },
              ),
              TextFormField(
                controller: _nameController,
                decoration: const InputDecoration(labelText: 'Customer Name'),
                validator: (value) {
                  if (value == null || value.isEmpty) {
                    return 'Please enter the customer name';
                  }
                  return null;
                },
              ),
              const SizedBox(height: 20),
              ElevatedButton(
                onPressed: () {
                  if (_formKey.currentState!.validate()) {
                    sendWhatsAppTemplate(
                      'whatsapp:${_phoneController.text}',
                      _nameController.text,
                    );
                  }
                },
                child: const Text('Send Message'),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
